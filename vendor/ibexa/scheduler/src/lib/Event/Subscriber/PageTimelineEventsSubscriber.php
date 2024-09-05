<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Event\Subscriber;

use DateTimeInterface;
use Ibexa\Contracts\AdminUi\Resolver\IconPathResolverInterface;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Scheduler\Repository\DateBasedPublishServiceInterface;
use Ibexa\Core\Helper\TranslationHelper;
use Ibexa\PageBuilder\PageBuilder\Timeline\Context\ContentEditContext;
use Ibexa\PageBuilder\PageBuilder\Timeline\Context\ContentTranslateContext;
use Ibexa\PageBuilder\PageBuilder\Timeline\Context\ContentViewContext;
use Ibexa\PageBuilder\PageBuilder\Timeline\Event\ContentTimelineEvent;
use Ibexa\PageBuilder\PageBuilder\Timeline\Event\TimelineEvents;
use Ibexa\Scheduler\Timeline\FuturePublicationEvent;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Adds future publication event to PageBuilder Timeline.
 */
class PageTimelineEventsSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\Contracts\Scheduler\Repository\DateBasedPublishServiceInterface */
    private $dateBasedPublisherService;

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    /** @var \Twig\Environment */
    private $templating;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \Ibexa\Core\Helper\TranslationHelper */
    private $translationHelper;

    /** @var \Ibexa\Contracts\AdminUi\Resolver\IconPathResolverInterface */
    private $iconPathResolver;

    public function __construct(
        DateBasedPublishServiceInterface $dateBasedPublisherService,
        TranslatorInterface $translator,
        Environment $templating,
        ContentTypeService $contentTypeService,
        TranslationHelper $translationHelper,
        IconPathResolverInterface $iconPathResolver
    ) {
        $this->dateBasedPublisherService = $dateBasedPublisherService;
        $this->translator = $translator;
        $this->templating = $templating;
        $this->contentTypeService = $contentTypeService;
        $this->translationHelper = $translationHelper;
        $this->iconPathResolver = $iconPathResolver;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            TimelineEvents::COLLECT_EVENTS => 'onCollectTimelineEvents',
        ];
    }

    /**
     * @param \Ibexa\PageBuilder\PageBuilder\Timeline\Event\ContentTimelineEvent $event
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function onCollectTimelineEvents(ContentTimelineEvent $event): void
    {
        $context = $event->getContext();

        if (
            !$context instanceof ContentViewContext
            && !$context instanceof ContentEditContext
            && !$context instanceof ContentTranslateContext
        ) {
            return;
        }

        $content = $context->getContent();
        $events = $event->getTimelineEvents();
        $contentInfo = $content->contentInfo;

        $scheduledVersions = $this->dateBasedPublisherService->getVersionsEntriesForContent($content->id);

        if (empty($scheduledVersions)) {
            return;
        }

        $contentType = $this->contentTypeService->loadContentType($contentInfo->contentTypeId);
        $eventName = $this->getEventName($content);
        $eventIcon = $this->iconPathResolver->resolve('publish-later');

        foreach ($scheduledVersions as $scheduledVersion) {
            $publicationDate = $scheduledVersion->date;

            $events[] = new FuturePublicationEvent(
                $eventName,
                $this->getEventDescription($eventName, $publicationDate, $content, $contentType),
                $publicationDate,
                $eventIcon,
                $scheduledVersion->versionInfo->versionNo
            );
        }

        $event->setTimelineEvents($events);
    }

    /**
     * @param string $eventName
     * @param \DateTimeInterface $publicationDate
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType
     *
     * @return string
     */
    private function getEventDescription(string $eventName, DateTimeInterface $publicationDate, Content $content, ContentType $contentType): string
    {
        return $this->templating->render(
            '@IbexaScheduler/page_builder/timeline/events/future_publication_event_description.twig',
            [
                'name' => $eventName,
                'date' => $publicationDate,
                'content' => $content,
                'content_type' => $contentType,
            ]
        );
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     *
     * @return string
     */
    private function getEventName(Content $content): string
    {
        return $this->translator->trans(
            /** @Desc("New version of %contentName% published") */
            'event.future_publication.title',
            [
                '%contentName%' => $this->translationHelper->getTranslatedContentName($content),
            ],
            'ibexa_page_builder_timeline_events'
        );
    }
}

class_alias(PageTimelineEventsSubscriber::class, 'EzSystems\DateBasedPublisher\Core\Event\Subscriber\PageTimelineEventsSubscriber');
