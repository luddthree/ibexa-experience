<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam\FieldType\ImageAsset;

use Ibexa\Connector\Dam\Variation\AssetVariationGeneratorRegistry;
use Ibexa\Connector\Dam\Variation\TransformationFactoryRegistry;
use Ibexa\Contracts\Connector\Dam\AssetIdentifier;
use Ibexa\Contracts\Connector\Dam\AssetService;
use Ibexa\Contracts\Connector\Dam\AssetSource;
use Ibexa\Contracts\Core\Repository\Strategy\ContentThumbnail\Field\FieldTypeBasedThumbnailStrategy;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\Content\Thumbnail;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\FieldType\ImageAsset\ImageAssetThumbnailStrategy;

final class ExternalAssetThumbnailStrategy implements FieldTypeBasedThumbnailStrategy
{
    /** @var \Ibexa\Core\FieldType\ImageAsset\ImageAssetThumbnailStrategy */
    private $innerImageAssetThumbnailStrategy;

    /** @var \Ibexa\Contracts\Connector\Dam\AssetService */
    private $assetService;

    /** @var \Ibexa\Connector\Dam\Variation\TransformationFactoryRegistry */
    private $transformationFactoryRegistry;

    /** @var \Ibexa\Connector\Dam\Variation\AssetVariationGeneratorRegistry */
    private $assetVariationGeneratorRegistry;

    /** @var string */
    private $variationName;

    public function __construct(
        ImageAssetThumbnailStrategy $innerImageAssetThumbnailStrategy,
        AssetService $assetService,
        TransformationFactoryRegistry $transformationFactoryRegistry,
        AssetVariationGeneratorRegistry $assetVariationGeneratorRegistry,
        string $variationName
    ) {
        $this->innerImageAssetThumbnailStrategy = $innerImageAssetThumbnailStrategy;
        $this->assetService = $assetService;
        $this->transformationFactoryRegistry = $transformationFactoryRegistry;
        $this->assetVariationGeneratorRegistry = $assetVariationGeneratorRegistry;
        $this->variationName = $variationName;
    }

    public function getFieldTypeIdentifier(): string
    {
        return $this->innerImageAssetThumbnailStrategy->getFieldTypeIdentifier();
    }

    public function getThumbnail(Field $field, ?VersionInfo $versionInfo = null): ?Thumbnail
    {
        /** @var \Ibexa\Connector\Dam\FieldType\ImageAsset\Value $value */
        $value = $field->value;
        if ($value->source === null) {
            return $this->innerImageAssetThumbnailStrategy->getThumbnail($field);
        }

        $asset = $this->assetService->get(
            new AssetIdentifier($value->destinationContentId),
            new AssetSource($value->source)
        );

        $transformation = $this
            ->transformationFactoryRegistry
            ->getFactory($asset->getSource())
            ->build($this->variationName);

        $variation = $this
            ->assetVariationGeneratorRegistry
            ->getVariationGenerator($asset->getSource())
            ->generate($asset, $transformation);

        return new Thumbnail([
            'resource' => $variation->getAssetUri()->getPath(),
        ]);
    }
}

class_alias(ExternalAssetThumbnailStrategy::class, 'Ibexa\Platform\Connector\Dam\FieldType\ImageAsset\ExternalAssetThumbnailStrategy');
