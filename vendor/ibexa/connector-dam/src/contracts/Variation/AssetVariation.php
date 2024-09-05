<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Connector\Dam\Variation;

use Ibexa\Contracts\Connector\Dam\Asset;
use Ibexa\Contracts\Connector\Dam\AssetIdentifier;
use Ibexa\Contracts\Connector\Dam\AssetMetadata;
use Ibexa\Contracts\Connector\Dam\AssetSource;
use Ibexa\Contracts\Connector\Dam\AssetUri;
use Ibexa\Contracts\Connector\Dam\ExternalAsset;

final class AssetVariation implements ExternalAsset
{
    /** @var \Ibexa\Contracts\Connector\Dam\Asset */
    private $originalAsset;

    /** @var \Ibexa\Contracts\Connector\Dam\AssetUri */
    private $assetUri;

    /** @var \Ibexa\Contracts\Connector\Dam\Variation\Transformation */
    private $transformation;

    public function __construct(
        Asset $originalAsset,
        AssetUri $assetUri,
        Transformation $transformation
    ) {
        $this->originalAsset = $originalAsset;
        $this->assetUri = $assetUri;
        $this->transformation = $transformation;
    }

    public function getIdentifier(): AssetIdentifier
    {
        return $this->originalAsset->getIdentifier();
    }

    public function getSource(): AssetSource
    {
        return $this->originalAsset->getSource();
    }

    public function getAssetUri(): AssetUri
    {
        return $this->assetUri;
    }

    public function getAssetMetadata(): AssetMetadata
    {
        return $this->originalAsset->getAssetMetadata();
    }

    public function getOriginalAsset(): Asset
    {
        return $this->originalAsset;
    }

    public function getTransformation(): Transformation
    {
        return $this->transformation;
    }
}

class_alias(AssetVariation::class, 'Ibexa\Platform\Contracts\Connector\Dam\Variation\AssetVariation');
