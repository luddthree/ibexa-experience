<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Event\Subscriber;

use Ibexa\FieldTypePage\Event\BlockResponseEvent;
use Ibexa\FieldTypePage\Event\BlockResponseEvents;
use Ibexa\FieldTypePage\FieldType\Page\Service\BlockService;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Throwable;

class BlockResponseSubscriber implements EventSubscriberInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private BlockService $blockService;

    public function __construct(
        BlockService $blockService
    ) {
        $this->blockService = $blockService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BlockResponseEvents::BLOCK_RESPONSE => ['onBlockResponse', 255],
        ];
    }

    public function onBlockResponse(BlockResponseEvent $event): void
    {
        $response = $event->getResponse();

        try {
            $response->setContent($this->blockService->render($event->getBlockContext(), $event->getBlockValue()));
        } catch (Throwable $e) {
            if (isset($this->logger)) {
                $this->logger->error($e->getMessage(), [
                    'exception' => $e,
                ]);
            }
        }
    }
}

class_alias(BlockResponseSubscriber::class, 'EzSystems\EzPlatformPageFieldType\Event\Subscriber\BlockResponseSubscriber');
