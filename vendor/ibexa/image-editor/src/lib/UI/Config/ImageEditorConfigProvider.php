<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ImageEditor\UI\Config;

use Ibexa\Contracts\AdminUi\UI\Config\ProviderInterface;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;

class ImageEditorConfigProvider implements ProviderInterface
{
    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    public function getConfig()
    {
        $actionGroups = $this->configResolver->getParameter('image_editor.action_groups');

        foreach ($actionGroups as $actionId => $actionGroup) {
            $actionGroups[$actionId]['actions'] = $this->filterAndSortActions($actionGroup);
        }

        return [
            'config' => [
                'actionGroups' => $actionGroups,
                'imageQuality' => $this->configResolver->getParameter('image_editor.image_quality'),
            ],
        ];
    }

    private function filterAndSortActions($actionGroup): array
    {
        $filteredActions = array_filter($actionGroup['actions'], static function (array $value): bool {
            return $value['visible'];
        });

        uasort($filteredActions, static function (array $a, array $b): int {
            return $b['priority'] <=> $a['priority'];
        });

        foreach ($filteredActions as $actionId => $action) {
            $buttons = array_filter($action['buttons'], static function (array $value): bool {
                return $value['visible'];
            });

            uasort($buttons, static function (array $a, array $b): int {
                return $b['priority'] <=> $a['priority'];
            });
            $filteredActions[$actionId]['buttons'] = $buttons;
        }

        return $filteredActions;
    }
}

class_alias(ImageEditorConfigProvider::class, 'Ibexa\Platform\ImageEditor\UI\Config\ImageEditorConfigProvider');
