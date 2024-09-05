<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Event\Subscriber;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Scheduler\Repository\DateBasedPublishServiceInterface;
use Ibexa\Core\MVC\Symfony\Event\PreContentViewEvent;
use Ibexa\Core\MVC\Symfony\MVCEvents;
use Ibexa\PageBuilder\View\PageView;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Adds future publication event to PageBuilder Timeline.
 */
class PageBuilderTemplatesSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\Contracts\Scheduler\Repository\DateBasedPublishServiceInterface */
    private $dateBasedPublisherService;

    /**
     * @param \Ibexa\Contracts\Scheduler\Repository\DateBasedPublishServiceInterface $dateBasedPublisherService
     */
    public function __construct(
        DateBasedPublishServiceInterface $dateBasedPublisherService
    ) {
        $this->dateBasedPublisherService = $dateBasedPublisherService;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            MVCEvents::PRE_CONTENT_VIEW => [
                ['onPrePageView', 0],
            ],
        ];
    }

    /**
     * @param \Ibexa\Core\MVC\Symfony\Event\PreContentViewEvent $event
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentType
     * @throws \Exception
     */
    public function onPrePageView(PreContentViewEvent $event): void
    {
        $view = $event->getContentView();

        if (!$view instanceof PageView) {
            return;
        }

        if (!$view->hasParameter('content')) {
            return;
        }

        $view->setTemplateIdentifier(
            '@IbexaScheduler/overrides/ibexa_page_builder/page_builder/preview.html.twig'
        );

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
        $content = $view->getParameter('content');

        $view->addParameters([
            'future_versions' => $this->getFutureVersions($content),
        ]);
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     *
     * @return int[]
     */
    private function getFutureVersions(Content $content): array
    {
        $scheduledContentVersions = $this->dateBasedPublisherService->getVersionsEntriesForContent($content->contentInfo->id);
        $versionNumbers = [];

        foreach ($scheduledContentVersions as $scheduledVersion) {
            $versionNumbers[] = $scheduledVersion->versionInfo->versionNo;
        }

        return $versionNumbers;
    }
}

class_alias(PageBuilderTemplatesSubscriber::class, 'EzSystems\DateBasedPublisher\Core\Event\Subscriber\PageBuilderTemplatesSubscriber');
