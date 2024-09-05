<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Twig;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\ProductCatalog\Values\Asset\AssetInterface;
use Twig\Environment;
use Twig\Extension\RuntimeExtensionInterface;

final class ProductAssetRuntime implements RuntimeExtensionInterface
{
    private const ASSET_PREVIEW_BLOCK_NAME = '%s_asset';

    private ConfigResolverInterface $configResolver;

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    /**
     * @param array<string,mixed> $parameters
     */
    public function renderAsset(Environment $env, AssetInterface $asset, array $parameters = []): ?string
    {
        $type = $this->getAssetType($asset);
        if ($type === null) {
            return null;
        }

        $blockName = sprintf(self::ASSET_PREVIEW_BLOCK_NAME, $type);

        $templates = $this->getAssetTemplates();
        if (isset($parameters['template'])) {
            array_unshift($templates, $parameters['template']);
            unset($parameters['template']);
        }

        foreach ($templates as $template) {
            $template = $env->load($template);
            if ($template->hasBlock($blockName)) {
                return $template->renderBlock(
                    $blockName,
                    ['asset' => $asset] + $parameters
                );
            }
        }

        return null;
    }

    public function getAssetType(AssetInterface $asset): ?string
    {
        return parse_url($asset->getUri(), PHP_URL_SCHEME) ?: null;
    }

    /**
     * @return string[]
     */
    public function getAssetTemplates(): array
    {
        $templates = $this->configResolver->getParameter('product_catalog.asset_templates');

        usort(
            $templates,
            static fn (array $a, array $b): int => $b['priority'] <=> $a['priority']
        );

        return array_column($templates, 'template');
    }
}
