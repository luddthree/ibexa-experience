<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Event\Subscriber;

use DateTime;
use Ibexa\FieldTypePage\FieldType\Page\Service\BlockService;
use Ibexa\PageBuilder\Event\BlockPreviewEvents;
use Ibexa\PageBuilder\Event\BlockPreviewResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BlockPreviewSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\FieldTypePage\FieldType\Page\Service\BlockService */
    private $blockService;

    /**
     * @param \Ibexa\FieldTypePage\FieldType\Page\Service\BlockService $blockService
     */
    public function __construct(BlockService $blockService)
    {
        $this->blockService = $blockService;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            BlockPreviewEvents::RESPONSE => [
                ['renderBlock', -200],
                ['addVisibilityData', -200],
            ],
        ];
    }

    /**
     * @param \Ibexa\PageBuilder\Event\BlockPreviewResponseEvent $event
     *
     * @throws \Exception
     */
    public function renderBlock(BlockPreviewResponseEvent $event): void
    {
        $renderedBlockData = [
            'html' => $this->blockService->render($event->getBlockContext(), $event->getBlockValue()),
        ];

        $event->setResponseData(array_merge($event->getResponseData(), $renderedBlockData));
    }

    /**
     * @param \Ibexa\PageBuilder\Event\BlockPreviewResponseEvent $event
     *
     * @throws \Exception
     */
    public function addVisibilityData(BlockPreviewResponseEvent $event): void
    {
        $pagePreviewParameters = $event->getPagePreviewParameters();
        $referenceDateTime = isset($pagePreviewParameters['referenceTimestamp'])
            ? DateTime::createFromFormat('U', $pagePreviewParameters['referenceTimestamp'])
            : null;

        $responseData = [
            'data' => [
                'visible' => $event->getBlockValue()->isVisible($referenceDateTime),
            ],
        ];

        $event->setResponseData(array_merge_recursive($event->getResponseData(), $responseData));
    }
}

class_alias(BlockPreviewSubscriber::class, 'EzSystems\EzPlatformPageBuilder\Event\Subscriber\BlockPreviewSubscriber');
