<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Connector\Dam;

final class Asset implements ExternalAsset
{
    /** @var \Ibexa\Contracts\Connector\Dam\AssetIdentifier */
    private $identifier;

    /** @var \Ibexa\Contracts\Connector\Dam\AssetSource */
    private $source;

    /** @var \Ibexa\Contracts\Connector\Dam\AssetUri */
    private $assetUri;

    /** @var \Ibexa\Contracts\Connector\Dam\AssetMetadata */
    private $assetMetadata;

    public function __construct(
        AssetIdentifier $identifier,
        AssetSource $source,
        AssetUri $assetUri,
        AssetMetadata $assetMetadata
    ) {
        $this->identifier = $identifier;
        $this->source = $source;
        $this->assetUri = $assetUri;
        $this->assetMetadata = $assetMetadata;
    }

    public function getIdentifier(): AssetIdentifier
    {
        return $this->identifier;
    }

    public function getSource(): AssetSource
    {
        return $this->source;
    }

    public function getAssetUri(): AssetUri
    {
        return $this->assetUri;
    }

    public function getAssetMetadata(): AssetMetadata
    {
        return $this->assetMetadata;
    }
}

class_alias(Asset::class, 'Ibexa\Platform\Contracts\Connector\Dam\Asset');
