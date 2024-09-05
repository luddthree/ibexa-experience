<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ImagePicker\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ImagePickerAdminUiOverridePass implements CompilerPassInterface
{
    private const UNIVERSAL_DISCOVERY_WIDGET_CONFIGURATION_PARAM_NAME = 'ibexa.site_access.config.default.universal_discovery_widget_module.configuration';

    private const UNIVERSAL_DISCOVERY_WIDGET_CONFIG_IMAGE_PICKER = 'image_picker';
    private const UNIVERSAL_DISCOVERY_WIDGET_CONFIG_RICHTEXT_EMBED_IMAGE = 'richtext_embed_image';
    private const UNIVERSAL_DISCOVERY_WIDGET_CONFIG_IMAGE_ASSET = 'image_asset';

    public function process(ContainerBuilder $container): void
    {
        $hasUdwConfig = $container->hasParameter(self::UNIVERSAL_DISCOVERY_WIDGET_CONFIGURATION_PARAM_NAME);

        if ($hasUdwConfig) {
            /** @var array<array<string, mixed>> $udwConfig */
            $udwConfig = $container->getParameter(self::UNIVERSAL_DISCOVERY_WIDGET_CONFIGURATION_PARAM_NAME);

            $imageConfigs = [
                self::UNIVERSAL_DISCOVERY_WIDGET_CONFIG_IMAGE_PICKER,
                self::UNIVERSAL_DISCOVERY_WIDGET_CONFIG_RICHTEXT_EMBED_IMAGE,
                self::UNIVERSAL_DISCOVERY_WIDGET_CONFIG_IMAGE_ASSET,
            ];

            foreach ($imageConfigs as $configName) {
                $this->setActiveTabInConfig($udwConfig, $configName);
            }

            $container->setParameter(self::UNIVERSAL_DISCOVERY_WIDGET_CONFIGURATION_PARAM_NAME, $udwConfig);
        }
    }

    /**
     * @param array<array<string, mixed>> $udwConfig
     */
    private function setActiveTabInConfig(array &$udwConfig, string $configName): void
    {
        if (!array_key_exists($configName, $udwConfig)) {
            return;
        }

        $udwConfig[$configName]['active_tab'] = 'image_picker';
    }
}
