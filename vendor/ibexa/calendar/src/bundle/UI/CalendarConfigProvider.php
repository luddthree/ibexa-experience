<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Calendar\UI;

use Ibexa\Calendar\EventType\EventTypeRegistryInterface;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Symfony\Component\Asset\Packages;

/**
 * Provides configuration necessary to render calendar component.
 */
final class CalendarConfigProvider
{
    /** @var \Ibexa\Calendar\EventType\EventTypeRegistryInterface */
    private $eventTypesRegistry;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    /** @var \Symfony\Component\Asset\Packages */
    private $packages;

    public function __construct(
        EventTypeRegistryInterface $eventTypesRegistry,
        ConfigResolverInterface $configResolver,
        Packages $packages
    ) {
        $this->eventTypesRegistry = $eventTypesRegistry;
        $this->configResolver = $configResolver;
        $this->packages = $packages;
    }

    public function getConfig(): array
    {
        return [
            'types' => $this->resolveEventTypesConfiguration(
                $this->configResolver->getParameter('calendar.event_types')
            ),
        ];
    }

    private function resolveEventTypesConfiguration(array $config): array
    {
        $resolvedConfig = [];

        /** @var \Ibexa\Contracts\Calendar\EventType\EventTypeInterface $type */
        foreach ($this->eventTypesRegistry->getTypes() as $type) {
            $identifier = $type->getTypeIdentifier();

            $actionsConfig = [];
            foreach ($type->getActions() as $action) {
                $actionIdentifier = $action->getActionIdentifier();

                $actionsConfig[$actionIdentifier] = [
                    'label' => $action->getActionLabel(),
                    'icon' => $this->getIconUrl($config[$identifier]['actions'][$actionIdentifier]['icon'] ?? null),
                ];
            }

            $resolvedConfig[$identifier] = [
                'label' => $type->getTypeLabel(),
                'color' => $config[$identifier]['color'] ?? null,
                'icon' => $this->getIconUrl($config[$identifier]['icon'] ?? null),
                'actions' => $actionsConfig,
                'isSelectable' => !empty($actionsConfig),
            ];
        }

        return $resolvedConfig;
    }

    private function getIconUrl(?string $icon): ?string
    {
        if ($icon === null) {
            return null;
        }

        $fragment = null;
        if (strpos($icon, '#') !== false) {
            [$icon, $fragment] = explode('#', $icon);
        }

        return $this->packages->getUrl($icon) . ($fragment ? '#' . $fragment : '');
    }
}

class_alias(CalendarConfigProvider::class, 'EzSystems\EzPlatformCalendarBundle\UI\CalendarConfigProvider');
