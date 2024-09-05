<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam\Twig\Extension;

use Ibexa\Contracts\Connector\Dam\AssetIdentifier;
use Ibexa\Contracts\Connector\Dam\AssetService;
use Ibexa\Contracts\Connector\Dam\AssetSource;
use Ibexa\Contracts\Connector\Dam\ExternalAsset;
use Ibexa\Contracts\Connector\Dam\Variation\Transformation;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class AssetExtension extends AbstractExtension
{
    /** @var \Ibexa\Contracts\Connector\Dam\AssetService */
    private $assetService;

    public function __construct(AssetService $assetService)
    {
        $this->assetService = $assetService;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction(
                'ibexa_platform_asset',
                [$this, 'getAsset'],
                [
                    'is_safe' => ['html'],
                    'deprecated' => '4.0',
                    'alternative' => 'ibexa_dam_asset',
                ]
            ),
            new TwigFunction(
                'ibexa_dam_asset',
                [$this, 'getAsset'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function getAsset(
        string $id,
        string $source,
        ?Transformation $transformation = null
    ): ExternalAsset {
        $asset = $this->assetService->get(
            new AssetIdentifier($id),
            new AssetSource($source)
        );

        if ($transformation !== null) {
            return $this->assetService->transform(
                $asset,
                $transformation
            );
        }

        return $asset;
    }
}

class_alias(AssetExtension::class, 'Ibexa\Platform\Connector\Dam\Twig\Extension\AssetExtension');
