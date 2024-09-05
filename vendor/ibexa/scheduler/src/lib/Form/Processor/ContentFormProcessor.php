<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Form\Processor;

use DateTime;
use Exception;
use Ibexa\ContentForms\Data\Content\ContentCreateData;
use Ibexa\ContentForms\Data\Content\ContentUpdateData;
use Ibexa\ContentForms\Event\FormActionEvent;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentStruct;
use Ibexa\Contracts\Scheduler\Repository\DateBasedPublishServiceInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Scheduler\Form\ContentEditTypeExtension;
use Ibexa\Scheduler\Form\Event\ContentEditEvents;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Listens for and processes RepositoryForm events.
 */
class ContentFormProcessor implements EventSubscriberInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /** @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface */
    private $urlGenerator;

    /** @var \Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface */
    private $notificationHandler;

    /** @var \Ibexa\Contracts\Scheduler\Repository\DateBasedPublishServiceInterface */
    private $scheduleService;

    public function __construct(
        ContentService $contentService,
        LocationService $locationService,
        UrlGeneratorInterface $urlGenerator,
        TranslatableNotificationHandlerInterface $notificationHandler,
        DateBasedPublishServiceInterface $scheduleService
    ) {
        $this->contentService = $contentService;
        $this->locationService = $locationService;
        $this->urlGenerator = $urlGenerator;
        $this->notificationHandler = $notificationHandler;
        $this->scheduleService = $scheduleService;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ContentEditEvents::CONTENT_SCHEDULE_PUBLISH => ['processSchedulePublish', 10],
            ContentEditEvents::CONTENT_DISCARD_SCHEDULE_PUBLISH => ['processDiscardSchedulePublish', 10],
        ];
    }

    public function processDiscardSchedulePublish(FormActionEvent $event): void
    {
        /** @var \Ibexa\ContentForms\Data\Content\ContentCreateData|\Ibexa\ContentForms\Data\Content\ContentUpdateData $data */
        $data = $event->getData();
        $form = $event->getForm();
        $languageCode = $form->getConfig()->getOption('languageCode');

        try {
            $contentDraft = $this->saveDraft($data, $languageCode);

            $this->scheduleService->unschedulePublish($contentDraft->versionInfo->id);
            $locationId = $contentDraft->versionInfo->contentInfo->mainLocationId;
            $contentId = $contentDraft->id;

            if (empty($locationId)) {
                $locations = $this->locationService->loadParentLocationsForDraftContent($contentDraft->versionInfo);
                $location = array_shift($locations);
                $locationId = $location->id;
                $contentId = $location->contentId;
            }

            $redirectUrl = $this->urlGenerator->generate('ibexa.content.view', [
                'contentId' => $contentId,
                'locationId' => $locationId,
            ]);

            $this->notificationHandler->success(
                /** @Desc("Canceled scheduled publication.") */
                'date_based_publisher.discard.publish_later',
                [],
                'ibexa_scheduler'
            );

            $event->setResponse(new RedirectResponse($redirectUrl));
        } catch (Exception $e) {
            $this->notificationHandler->error(
                /** @Desc("Cannot cancel future publication: %reason%") */
                'date_based_publisher.error.discard.publish_later',
                ['%reason%' => $e->getMessage()],
                'ibexa_scheduler'
            );

            $event->setResponse(
                new RedirectResponse(
                    $this->getContentEditUrl($data, $languageCode)
                )
            );
        }
    }

    /**
     * @param \Ibexa\ContentForms\Event\FormActionEvent $event
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentException
     */
    public function processSchedulePublish(FormActionEvent $event): void
    {
        /** @var \Ibexa\ContentForms\Data\Content\ContentCreateData|\Ibexa\ContentForms\Data\Content\ContentUpdateData $data */
        $data = $event->getData();
        $form = $event->getForm();
        $languageCode = $form->getConfig()->getOption('languageCode');

        $scheduleLater = $form->get(ContentEditTypeExtension::EXTENSION_NAME);

        try {
            $contentDraft = $this->saveDraft($data, $languageCode);

            $locationId = $contentDraft->versionInfo->contentInfo->mainLocationId;
            $contentId = $contentDraft->id;
            $versionId = $contentDraft->versionInfo->id;

            if (empty($locationId)) {
                $locations = $this->locationService->loadParentLocationsForDraftContent($contentDraft->versionInfo);
                $location = array_shift($locations);
                $locationId = $location->id;
                $contentId = $location->contentId;
            }

            $redirectUrl = $this->urlGenerator->generate('ibexa.content.view', [
                'contentId' => $contentId,
                'locationId' => $locationId,
            ]);

            $when = DateTime::createFromFormat('U', $scheduleLater['timestamp']->getData());

            if ($this->scheduleService->isScheduledPublish($versionId)) {
                $scheduledVersion = $this->scheduleService->getScheduledPublish($versionId);

                $this->scheduleService->updateScheduledPublish($scheduledVersion, $when);
            } else {
                $this->scheduleService->schedulePublish($contentDraft->versionInfo, $when);
            }

            $this->notificationHandler->success(
                /** @Desc("Scheduled content for publication.") */
                'date_based_publisher.success.publish_later',
                [],
                'ibexa_scheduler'
            );

            $event->setResponse(new RedirectResponse($redirectUrl));
        } catch (Exception $e) {
            $this->notificationHandler->error(
                /** @Desc("Cannot schedule content: %reason%") */
                'date_based_publisher.error.publish_later',
                ['%reason%' => $e->getMessage()],
                'ibexa_scheduler'
            );

            $event->setResponse(
                new RedirectResponse(
                    $this->getContentEditUrl($data, $languageCode)
                )
            );
        }
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
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentFieldValidationException
     */
    private function saveDraft(ContentStruct $data, string $languageCode): Content
    {
        if (false === ($data instanceof ContentUpdateData) && false === ($data instanceof ContentCreateData)) {
            throw new InvalidArgumentException(
                '$data',
                'Expected ContentUpdateData or ContentCreateData'
            );
        }

        $mainLanguageCode = $this->resolveMainLanguageCode($data);

        foreach ($data->fieldsData as $fieldDefIdentifier => $fieldData) {
            if ($mainLanguageCode !== $languageCode && false === $fieldData->fieldDefinition->isTranslatable) {
                continue;
            }

            $data->setField($fieldDefIdentifier, $fieldData->value, $languageCode);
        }

        if ($data->isNew()) {
            $contentDraft = $this->contentService->createContent($data, $data->getLocationStructs());
        } else {
            $contentDraft = $this->contentService->updateContent($data->contentDraft->getVersionInfo(), $data);
        }

        return $contentDraft;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\ContentStruct|\Ibexa\ContentForms\Data\Content\ContentCreateData|\Ibexa\ContentForms\Data\Content\ContentUpdateData $data
     *
     * @return string
     */
    private function resolveMainLanguageCode(ContentStruct $data): string
    {
        return $data->isNew()
            ? $data->mainLanguageCode
            : $data->contentDraft->getVersionInfo()->getContentInfo()->mainLanguageCode;
    }

    /**
     * Returns content create or edit URL depending on $data type.
     *
     * @param \Ibexa\ContentForms\Data\Content\ContentData|\Ibexa\ContentForms\Data\Content\ContentCreateData|\Ibexa\ContentForms\Data\Content\ContentUpdateData $data
     * @param string $languageCode
     *
     * @return string
     *
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     */
    private function getContentEditUrl($data, string $languageCode): string
    {
        return $data->isNew()
            ? $this->urlGenerator->generate('ibexa.content.create.proxy', [
                'parentLocationId' => $data->getLocationStructs()[0]->parentLocationId,
                'contentTypeIdentifier' => $data->contentType->identifier,
                'languageCode' => $languageCode,
            ])
            : $this->urlGenerator->generate('ibexa.content.draft.edit', [
                'contentId' => $data->contentDraft->id,
                'versionNo' => $data->contentDraft->getVersionInfo()->versionNo,
                'language' => $languageCode,
            ]);
    }
}

class_alias(ContentFormProcessor::class, 'EzSystems\DateBasedPublisher\Core\Form\Processor\ContentFormProcessor');
