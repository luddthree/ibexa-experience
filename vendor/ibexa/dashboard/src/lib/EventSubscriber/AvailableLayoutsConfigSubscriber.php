<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\EventSubscriber;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Dashboard\Specification\IsDashboardContentType;
use Ibexa\FieldTypePage\Event\AvailableLayoutsConfigEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @phpstan-import-type TLayoutDefinition from \Ibexa\FieldTypePage\FieldType\LandingPage\Converter\LayoutDefinitionConverter
 */
final class AvailableLayoutsConfigSubscriber implements EventSubscriberInterface
{
    private const LAYOUTS_TO_FILTER = [
        'dashboard_one_column',
        'dashboard_two_columns',
        'dashboard_one_third_left',
        'dashboard_one_third_right',
        'dashboard_two_rows_two_columns',
        'dashboard_three_rows_two_columns',
        'dashboard_three_rows_two_columns_2',
        'dashboard_three_columns',
        'dashboard_two_rows_three_columns',
        'dashboard_three_rows_three_columns',
        'dashboard_three_rows_three_columns_2',
    ];

    private ConfigResolverInterface $configResolver;

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AvailableLayoutsConfigEvent::class => ['onAvailableLayoutsConfig', -10],
        ];
    }

    public function onAvailableLayoutsConfig(AvailableLayoutsConfigEvent $event): void
    {
        $layouts = $event->getLayouts();
        $contentType = $event->getContentType();

        if ($contentType === null) {
            return;
        }

        if ((new IsDashboardContentType($this->configResolver))->isSatisfiedBy($contentType)) {
            return;
        }

        $filteredLayouts = array_filter(
            $layouts,
            /**
             * @param array<TLayoutDefinition> $layout
             */
            static function (array $layout): bool {
                return !in_array($layout['id'], self::LAYOUTS_TO_FILTER, true);
            }
        );

        $event->setLayouts($filteredLayouts);
    }
}
