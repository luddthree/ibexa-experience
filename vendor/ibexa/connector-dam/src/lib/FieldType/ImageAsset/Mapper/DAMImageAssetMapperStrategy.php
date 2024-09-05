<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Connector\Dam\FieldType\ImageAsset\Mapper;

use Ibexa\Contracts\Connector\Dam\AssetIdentifier;
use Ibexa\Contracts\Connector\Dam\AssetService;
use Ibexa\Contracts\Connector\Dam\AssetSource;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Core\FieldType\Image;
use Ibexa\Core\FieldType\ImageAsset;
use Ibexa\GraphQL\Mapper\ImageAssetMapperStrategyInterface;

final class DAMImageAssetMapperStrategy implements ImageAssetMapperStrategyInterface
{
    private AssetService $assetService;

    public function __construct(AssetService $assetService)
    {
        return $this->assetService = $assetService;
    }

    public function canProcess(ImageAsset\Value $value): bool
    {
        return $value->source !== null;
    }

    /**
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentType
     */
    public function process(ImageAsset\Value $value): Field
    {
        $asset = $this->assetService->get(
            new AssetIdentifier($value->destinationContentId),
            new AssetSource($value->source)
        );

        return new Field([
            'id' => $value->destinationContentId,
            'value' => new Image\Value([
                'id' => $asset->getIdentifier()->getId(),
                'fileName' => $asset->getIdentifier()->getId(),
                'uri' => $asset->getAssetUri()->getPath(),
                'width' => $asset->getAssetMetadata()['width'],
                'height' => $asset->getAssetMetadata()['height'],
                'alternativeText' => $asset->getAssetMetadata()['alternativeText'],
            ]),
        ]);
    }
}
