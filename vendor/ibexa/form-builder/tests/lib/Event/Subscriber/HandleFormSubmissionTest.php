<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\FormBuilder\Event\Subscriber;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\FormBuilder\FieldType\Model\Attribute;
use Ibexa\Contracts\FormBuilder\FieldType\Model\Field as FormBuilderFieldType;
use Ibexa\Contracts\FormBuilder\FieldType\Model\Form;
use Ibexa\Contracts\FormBuilder\FieldType\Model\FormSubmission;
use Ibexa\Contracts\FormBuilder\FormSubmission\FormSubmissionServiceInterface;
use Ibexa\Core\MVC\Symfony\Event\PreContentViewEvent;
use Ibexa\Core\MVC\Symfony\View\ContentView;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\FormBuilder\Event\FormActionEvent;
use Ibexa\FormBuilder\Event\FormEvents;
use Ibexa\FormBuilder\Event\FormSubmitEvent;
use Ibexa\FormBuilder\Event\Subscriber\HandleFormSubmission;
use Ibexa\FormBuilder\FieldType\Type;
use Ibexa\FormBuilder\FieldType\Value;
use Ibexa\FormBuilder\FormSubmission\Notification\NotificationSenderInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Button;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class HandleFormSubmissionTest extends TestCase
{
    public const LANGUAGE_CODE = 'cyb-CY';

    private RequestStack $requestStack;

    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $eventDispatcher;

    /** @var \Ibexa\Contracts\FormBuilder\FormSubmission\FormSubmissionServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $formSubmissionService;

    /** @var \Ibexa\FormBuilder\FieldType\Type|\PHPUnit\Framework\MockObject\MockObject */
    private $formFieldType;

    /** @var \Ibexa\FormBuilder\FormSubmission\Notification\NotificationSenderInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $notificationSender;

    protected function setUp(): void
    {
        parent::setUp();

        $mainRequest = $this->createMock(Request::class);
        $mainRequest->attributes = new ParameterBag();

        $this->requestStack = new RequestStack();
        $this->requestStack->push($mainRequest);

        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->formSubmissionService = $this->createMock(FormSubmissionServiceInterface::class);
        $this->formFieldType = $this->createMock(Type::class);
        $this->notificationSender = $this->createMock(NotificationSenderInterface::class);
    }

    public function testHandleFormSubmission(): void
    {
        $data = [
            'fields' => [
                1 => 'value1',
                2 => 'value2',
            ],
            'languageCode' => self::LANGUAGE_CODE,
        ];

        $form = $this->getFormMock($data);
        $formValue = $this->getFormValue();

        $value = new Value($formValue, $form);

        $field1 = $this->getField('identifier1', $value);
        $field2 = $this->getField('identifier2');

        $submission = $this->createMock(FormSubmission::class);

        $content = $this->getCompleteContent([$field1, $field2]);

        $contentView = new ContentView(null, [], 'full');
        $contentView->setContent($content);

        $formSubmitEvent = new FormSubmitEvent(
            $contentView,
            $formValue,
            $data
        );

        $this->formSubmissionService
            ->expects(self::once())
            ->method('create')
            ->willReturn($submission);

        $this->eventDispatcher
            ->expects(self::at(0))
            ->method('dispatch')
            ->with($formSubmitEvent, FormEvents::FORM_SUBMIT)
            ->willReturn($formSubmitEvent);

        $this->eventDispatcher
            ->expects(self::at(1))
            ->method('dispatch')
            ->with($formSubmitEvent, 'ezplatform.form_builder.form_submit.content_id.123')
            ->willReturn($formSubmitEvent);

        $formActionEvent = new FormActionEvent(
            $contentView,
            $formValue,
            'action',
            'action'
        );

        $this->eventDispatcher
            ->expects(self::at(2))
            ->method('dispatch')
            ->with($formActionEvent, 'ezplatform.form_builder.form_submit.action.action')
            ->willReturn($formSubmitEvent);

        $this->notificationSender
            ->expects(self::once())
            ->method('sendNotification')
            ->with($content, $formValue, $submission, $formValue->getFieldById('2'));

        $this->handle($contentView);

        $masterRequest = $this->requestStack->getMainRequest();
        self::assertTrue($masterRequest->attributes->get($form->getName() . '_submitted', false));
    }

    public function testHandleFormSubmissionWithoutFormFieldType(): void
    {
        $content = $this->getCompleteContent();

        $contentView = new ContentView(null, [], 'full');
        $contentView->setContent($content);

        $this->eventDispatcher
            ->expects(self::never())
            ->method('dispatch');

        $this->notificationSender
            ->expects(self::never())
            ->method('sendNotification');

        $this->handle($contentView);
    }

    public function testHandleFormSubmissionWithoutForm(): void
    {
        $formValue = $this->getFormValue();

        $value = new Value($formValue, null);

        $field1 = $this->getField('identifier1', $value);
        $field2 = $this->getField('identifier2');

        $content = $this->getCompleteContent([$field1, $field2]);

        $contentView = new ContentView(null, [], 'full');
        $contentView->setContent($content);

        $this->eventDispatcher
            ->expects(self::never())
            ->method('dispatch');

        $this->notificationSender
            ->expects(self::never())
            ->method('sendNotification');

        $this->handle($contentView);
    }

    public function testHandleFormSubmissionWithoutContent(): void
    {
        $contentView = new ContentView(null, [], 'full');

        $this->eventDispatcher
            ->expects(self::never())
            ->method('dispatch');

        $this->notificationSender
            ->expects(self::never())
            ->method('sendNotification');

        $this->handle($contentView);
    }

    /**
     * @param $contentView
     *
     * @throws \Ibexa\FormBuilder\Exception\FormFieldNotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    private function handle($contentView)
    {
        $handleFormSubmission = new HandleFormSubmission(
            $this->requestStack,
            $this->eventDispatcher,
            $this->formSubmissionService,
            $this->formFieldType,
            $this->notificationSender
        );

        $event = new PreContentViewEvent($contentView);

        $handleFormSubmission->handleFormSubmission($event);
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Field[] $fields
     *
     * @return \Ibexa\Core\Repository\Values\Content\Content
     */
    private function getCompleteContent(array $fields = []): Content
    {
        return new Content([
            'internalFields' => $fields,
            'versionInfo' => new VersionInfo([
                'contentInfo' => new ContentInfo(['mainLanguageCode' => self::LANGUAGE_CODE, 'id' => 123]),
            ]),
        ]);
    }

    /**
     * @param string $identifier
     * @param string $value
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Field
     */
    private function getField($identifier = 'identifier', $value = 'value'): Field
    {
        return new Field(
            [
                'fieldDefIdentifier' => $identifier,
                'languageCode' => self::LANGUAGE_CODE,
                'value' => $value,
            ]
        );
    }

    /**
     * @param array $data
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\Form\FormInterface
     */
    private function getFormMock(array $data = [])
    {
        $form = $this
            ->getMockBuilder('Symfony\Component\Form\Form')
            ->setConstructorArgs([$this->getBuilder('nested')])
            ->setMethods(['getClickedButton', 'isValid', 'isSubmitted', 'getData', 'handleRequest'])
            ->getMock();

        $button = $this->createMock(Button::class);
        $button->expects(self::once())
            ->method('getName')
            ->willReturn('2');

        $form->expects(self::once())
            ->method('isValid')
            ->willReturn(true);
        $form->expects(self::once())
            ->method('isSubmitted')
            ->willReturn(true);
        $form->expects(self::once())
            ->method('getData')
            ->willReturn($data);
        $form->expects(self::once())
            ->method('getClickedButton')
            ->willReturn($button);

        return $form;
    }

    /**
     * @return \Ibexa\Contracts\FormBuilder\FieldType\Model\Form
     */
    private function getFormValue(): Form
    {
        $fieldAttribute = new Attribute('action', json_encode(['action' => 'action']));

        $fields = [
            new FormBuilderFieldType('1', 'identifier1', 'name1'),
            new FormBuilderFieldType('2', 'identifier2', 'name2', [$fieldAttribute]),
        ];

        return new Form(null, null, null, $fields);
    }

    /**
     * @param string $name
     * @param null $dataClass
     * @param array $options
     *
     * @return \Symfony\Component\Form\FormBuilder
     */
    protected function getBuilder($name = 'name', $dataClass = null, array $options = []): FormBuilder
    {
        $factory = $this->getMockBuilder('Symfony\Component\Form\FormFactoryInterface')->getMock();
        $dispatcher = new EventDispatcher();

        return new FormBuilder($name, $dataClass, $dispatcher, $factory, $options);
    }
}

class_alias(HandleFormSubmissionTest::class, 'EzSystems\EzPlatformFormBuilder\Tests\Event\Subscriber\HandleFormSubmissionTest');
