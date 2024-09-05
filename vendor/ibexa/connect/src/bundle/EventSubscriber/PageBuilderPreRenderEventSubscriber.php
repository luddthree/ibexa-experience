<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Connect\EventSubscriber;

use Ibexa\Connect\PageBuilder\BlockClient;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\BlockRenderEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Twig\TwigRenderRequest;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class PageBuilderPreRenderEventSubscriber implements EventSubscriberInterface
{
    public const IBEXA_CONNECT_BLOCK = 'ibexa_connect_block';

    private BlockClient $blockClient;

    public function __construct(
        BlockClient $blockClient
    ) {
        $this->blockClient = $blockClient;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BlockRenderEvents::getBlockPreRenderEventName(self::IBEXA_CONNECT_BLOCK) => 'preRenderEvent',
        ];
    }

    public function preRenderEvent(PreRenderEvent $event): void
    {
        $renderRequest = $event->getRenderRequest();

        if (!$renderRequest instanceof TwigRenderRequest) {
            return;
        }

        $blockValue = $event->getBlockValue();
        $data = $this->blockClient->getExtraBlockData($blockValue);

        $renderRequest->addParameter('ibexa_connect_data', $data);
    }
}
