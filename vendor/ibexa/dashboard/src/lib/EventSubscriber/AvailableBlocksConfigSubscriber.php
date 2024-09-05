<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\EventSubscriber;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Dashboard\Specification\IsDashboardContentType;
use Ibexa\FieldTypePage\Event\AvailableBlocksConfigEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @phpstan-import-type TBlockDefinition from \Ibexa\FieldTypePage\FieldType\LandingPage\Converter\BlockDefinitionConverter
 */
final class AvailableBlocksConfigSubscriber implements EventSubscriberInterface
{
    private ConfigResolverInterface $configResolver;

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AvailableBlocksConfigEvent::class => ['onAvailableBlocksConfig', -10],
        ];
    }

    public function onAvailableBlocksConfig(AvailableBlocksConfigEvent $event): void
    {
        $blocks = $event->getBlocks();
        $contentType = $event->getContentType();

        if ($contentType === null) {
            return;
        }

        if ((new IsDashboardContentType($this->configResolver))->isSatisfiedBy($contentType)) {
            return;
        }

        $filteredBlocks = array_filter(
            $blocks,
            /**
             * @param array<TBlockDefinition> $block
             */
            static function (array $block): bool {
                return $block['category'] !== 'Dashboard';
            }
        );

        $event->setBlocks($filteredBlocks);
    }
}
