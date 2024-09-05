<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Event\Subscriber;

use Ibexa\ContentForms\Event\ContentFormEvents;
use Ibexa\ContentForms\Event\FormActionEvent;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Exceptions\ContentFieldValidationException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentStruct;
use Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface;
use Ibexa\Contracts\Workflow\Service\WorkflowServiceInterface;
use Ibexa\Workflow\Exception\NotFoundException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FormProcessor implements EventSubscriberInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Workflow\Service\WorkflowServiceInterface */
    private $workflowService;

    /** @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface */
    private $urlGenerator;

    /** @var \Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface */
    private $workflowRegistry;

    public function __construct(
        ContentService $contentService,
        WorkflowServiceInterface $workflowService,
        WorkflowRegistryInterface $workflowRegistry,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->contentService = $contentService;
        $this->workflowService = $workflowService;
        $this->urlGenerator = $urlGenerator;
        $this->workflowRegistry = $workflowRegistry;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ContentFormEvents::CONTENT_EDIT => ['onContentEdit', 100],
        ];
    }

    public function onContentEdit(FormActionEvent $event): void
    {
        $action = $event->getClickedButton();

        if ($action !== 'apply') {
            return;
        }

        /** @var \Ibexa\ContentForms\Data\Content\ContentCreateData|\Ibexa\ContentForms\Data\Content\ContentUpdateData $data */
        $data = $event->getData();
        $form = $event->getForm();
        $languageCode = $form->getConfig()->getOption('languageCode');

        $draft = $this->saveDraft($event->getData(), $languageCode, []);

        $workflowData = $form->get('workflow')->getData();

        $workflowName = $workflowData['name'];
        $transitionName = $workflowData['transition'];

        try {
            $workflowMetadata = $this->workflowService->loadWorkflowMetadataForContent($draft, $workflowName);
        } catch (NotFoundException $e) {
            $workflowMetadata = $this->workflowService->start($draft, $workflowName);
        }

        if (!$this->workflowService->can($workflowMetadata, $transitionName)) {
            // @todo error on transition not possible
            // @todo show notification
            return;
        }

        /** @var string $comment */
        $comment = $form->get('workflow')->get('comment')->getData() ?? '';
        $reviewerId = $form->get('workflow')->get('reviewer')->getData() ?? null;

        $workflow = $this->workflowRegistry->getWorkflow($workflowName);

        try {
            $workflow->apply($workflowMetadata->content, $transitionName, [
                'message' => $comment,
                'reviewerId' => $reviewerId,
            ]);

            $redirectionUrl = $this->getRedirectionUrl($event, $draft);
        } catch (ContentFieldValidationException $e) {
            $redirectionUrl = $this->urlGenerator->generate(
                'ibexa.content.draft.edit',
                [
                    'contentId' => $draft->id,
                    'versionNo' => $draft->versionInfo->versionNo,
                    'language' => $languageCode,
                    'validate' => true,
                ]
            );
        }

        $event->setResponse(new RedirectResponse($redirectionUrl));
    }

    /**
     * Saves content draft corresponding to $data.
     * Depending on the nature of $data (create or update data), the draft will either be created or simply updated.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\ContentStruct|\Ibexa\ContentForms\Data\Content\ContentCreateData|\Ibexa\ContentForms\Data\Content\ContentUpdateData $data
     * @param string $languageCode
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Content
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentFieldValidationException
     */
    private function saveDraft(ContentStruct $data, string $languageCode, ?array $fieldIdentifiersToValidate = null): Content
    {
        $mainLanguageCode = $data->isNew()
            ? $data->mainLanguageCode
            : $data->contentDraft->getVersionInfo()->getContentInfo()->mainLanguageCode;

        foreach ($data->fieldsData as $fieldDefIdentifier => $fieldData) {
            if ($mainLanguageCode !== $languageCode && !$fieldData->fieldDefinition->isTranslatable) {
                continue;
            }
            $data->setField($fieldDefIdentifier, $fieldData->value, $languageCode);
        }

        if ($data->isNew()) {
            $contentDraft = $this->contentService->createContent($data, $data->getLocationStructs(), $fieldIdentifiersToValidate);
        } else {
            $contentDraft = $this->contentService->updateContent($data->contentDraft->getVersionInfo(), $data, $fieldIdentifiersToValidate);
        }

        return $contentDraft;
    }

    /**
     * @param \Ibexa\Contracts\AdminUi\Event\FormActionEvent $event
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $draft
     *
     * @return string
     */
    private function getRedirectionUrl(FormActionEvent $event, Content $draft): string
    {
        $referrerLocation = $event->getOption('referrerLocation');
        $contentInfo = $draft->contentInfo;

        if ($contentInfo->isPublished()) {
            $redirectionUrl = $this->urlGenerator->generate(
                'ibexa.content.view',
                [
                    'contentId' => $referrerLocation ? $referrerLocation->contentId : $contentInfo->id,
                    'locationId' => $referrerLocation ? $referrerLocation->id : $contentInfo->mainLocationId,
                ]
            );
        } else {
            $redirectionUrl = $this->urlGenerator->generate('ibexa.dashboard');
        }

        return $redirectionUrl;
    }
}

class_alias(FormProcessor::class, 'EzSystems\EzPlatformWorkflow\Event\Subscriber\FormProcessor');
