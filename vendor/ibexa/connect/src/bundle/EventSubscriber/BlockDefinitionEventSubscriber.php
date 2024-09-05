<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Connect\EventSubscriber;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\Event\BlockDefinitionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class BlockDefinitionEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            BlockDefinitionEvents::getBlockDefinitionEventName(
                PageBuilderPreRenderEventSubscriber::IBEXA_CONNECT_BLOCK
            ) => ['onBlockDefinition', -10],
        ];
    }

    public function onBlockDefinition(BlockDefinitionEvent $event): void
    {
        $definition = $event->getDefinition();

        if (!empty($definition->getViews())) {
            return;
        }

        // Ibexa Connect block doesn't provide default templates and the block is unusable
        // unless it's properly configured to reflect client specific Connect configuration.
        $definition->setVisible(false);
    }
}
