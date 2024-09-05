<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Event\Subscriber;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\FormBuilder\FormSubmission\FormSubmissionServiceInterface;
use Ibexa\Core\MVC\Symfony\Event\PreContentViewEvent;
use Ibexa\Core\MVC\Symfony\MVCEvents;
use Ibexa\Core\MVC\Symfony\View\ContentView;
use Ibexa\FormBuilder\Event\FormActionEvent;
use Ibexa\FormBuilder\Event\FormEvents;
use Ibexa\FormBuilder\Event\FormSubmitEvent;
use Ibexa\FormBuilder\FieldType\Type;
use Ibexa\FormBuilder\FormSubmission\Notification\NotificationSenderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class HandleFormSubmission implements EventSubscriberInterface
{
    /** @var \Symfony\Component\HttpFoundation\RequestStack */
    private $requestStack;

    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    private $eventDispatcher;

    /** @var \Ibexa\Contracts\FormBuilder\FormSubmission\FormSubmissionServiceInterface */
    private $formSubmissionService;

    /** @var \Ibexa\FormBuilder\FieldType\Type */
    private $formFieldType;

    /** @var \Ibexa\FormBuilder\FormSubmission\Notification\NotificationSenderInterface */
    private $notificationSender;

    /**
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \Ibexa\Contracts\FormBuilder\FormSubmission\FormSubmissionServiceInterface $formSubmissionService
     * @param \Ibexa\FormBuilder\FieldType\Type $formFieldType
     * @param \Ibexa\FormBuilder\FormSubmission\Notification\NotificationSenderInterface $emailNotificationSender
     */
    public function __construct(
        RequestStack $requestStack,
        EventDispatcherInterface $eventDispatcher,
        FormSubmissionServiceInterface $formSubmissionService,
        Type $formFieldType,
        NotificationSenderInterface $emailNotificationSender
    ) {
        $this->requestStack = $requestStack;
        $this->eventDispatcher = $eventDispatcher;
        $this->formSubmissionService = $formSubmissionService;
        $this->formFieldType = $formFieldType;
        $this->notificationSender = $emailNotificationSender;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            MVCEvents::PRE_CONTENT_VIEW => 'handleFormSubmission',
        ];
    }

    /**
     * @throws \Ibexa\FormBuilder\Exception\FormFieldNotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function handleFormSubmission(PreContentViewEvent $event)
    {
        $contentView = $event->getContentView();

        if (!$contentView instanceof ContentView) {
            return;
        }

        $content = $contentView->getContent();
        $formField = $this->getFormFieldType($content);

        if (empty($formField)) {
            return;
        }

        /** @var \Ibexa\FormBuilder\FieldType\Value $formFieldValue */
        $formFieldValue = $formField->getValue();

        if (empty($formFieldValue->getForm())) {
            return;
        }

        $mainRequest = $this->requestStack->getMainRequest();

        /** @var \Symfony\Component\Form\Form $form */
        $form = $formFieldValue->getForm();
        $form->handleRequest($mainRequest);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return;
        }

        $data = $form->getData();
        $formValue = $formFieldValue->getFormValue();

        $event = new FormSubmitEvent(
            $contentView,
            $formValue,
            $data,
        );

        $this->eventDispatcher->dispatch($event, FormEvents::FORM_SUBMIT);
        $this->eventDispatcher->dispatch($event, FormEvents::getSubmitContentEventName($content->id));

        $data = $event->getData();

        $values = [];

        foreach ($data['fields'] as $id => $value) {
            $field = $formValue->getFieldById((string)$id);

            $values[] = [
                'id' => $id,
                'identifier' => $field->getIdentifier(),
                'name' => $field->getName(),
                'value' => $value,
            ];
        }

        $formRequestAttributeKey = $form->getName() . '_submitted';
        $isFormAlreadySubmitted = $mainRequest->attributes->get($formRequestAttributeKey, false);
        if (!$isFormAlreadySubmitted) {
            $mainRequest->attributes->set($formRequestAttributeKey, true);
            $submission = $this->formSubmissionService->create(
                $content->contentInfo,
                $data['languageCode'],
                $formValue,
                $values
            );
        }

        $clickedButton = $form->getClickedButton();

        if (empty($clickedButton)) {
            return;
        }

        $field = $formValue->getFieldById($clickedButton->getName());
        $actionAttribute = $field->getAttribute('action');

        $actionName = null;
        if ($actionAttribute->getValue()) {
            $action = json_decode(
                $actionAttribute->getValue(),
                true,
                2,
                JSON_OBJECT_AS_ARRAY
            );
            $actionName = $action['action'] ?? null;
        }

        if (empty($actionName)) {
            return;
        }

        $formActionEvent = new FormActionEvent(
            $contentView,
            $formValue,
            $actionName,
            $action[$actionName] ?? null
        );

        $this->eventDispatcher->dispatch($formActionEvent, FormEvents::getSubmitActionEventName($actionName));

        if (!$isFormAlreadySubmitted) {
            $this->notificationSender->sendNotification($content, $formValue, $submission, $field);
        }
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content|null $content
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Field|null
     */
    protected function getFormFieldType(?Content $content): ?Field
    {
        if (empty($content)) {
            return null;
        }

        foreach ($content->getFieldsByLanguage() as $field) {
            if ($field->fieldTypeIdentifier === $this->formFieldType->getFieldTypeIdentifier()) {
                return $field;
            }
        }

        return null;
    }
}

class_alias(HandleFormSubmission::class, 'EzSystems\EzPlatformFormBuilder\Event\Subscriber\HandleFormSubmission');
