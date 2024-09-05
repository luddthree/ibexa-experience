<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam;

use Ibexa\Connector\Dam\Handler\HandlerRegistry;
use Ibexa\Connector\Dam\Variation\AssetVariationGeneratorRegistry;
use Ibexa\Contracts\Connector\Dam\Asset;
use Ibexa\Contracts\Connector\Dam\AssetIdentifier;
use Ibexa\Contracts\Connector\Dam\AssetService as AssetServiceInterface;
use Ibexa\Contracts\Connector\Dam\AssetSource;
use Ibexa\Contracts\Connector\Dam\Search\AssetSearchResult;
use Ibexa\Contracts\Connector\Dam\Search\Query;
use Ibexa\Contracts\Connector\Dam\Variation\AssetVariation;
use Ibexa\Contracts\Connector\Dam\Variation\Transformation;

final class AssetService implements AssetServiceInterface
{
    /** @var \Ibexa\Connector\Dam\Handler\HandlerRegistry */
    private $handlerRegistry;

    /** @var \Ibexa\Connector\Dam\Variation\AssetVariationGeneratorRegistry */
    private $assetVariationGeneratorRegistry;

    public function __construct(
        HandlerRegistry $handlerRegistry,
        AssetVariationGeneratorRegistry $assetVariationGeneratorRegistry
    ) {
        $this->handlerRegistry = $handlerRegistry;
        $this->assetVariationGeneratorRegistry = $assetVariationGeneratorRegistry;
    }

    public function search(Query $query, AssetSource $assetSource, int $offset = 0, int $limit = 20): AssetSearchResult
    {
        return $this
            ->handlerRegistry
            ->getHandler($assetSource)
            ->search($query, $offset, $limit);
    }

    public function get(AssetIdentifier $identifier, AssetSource $source): Asset
    {
        return $this
            ->handlerRegistry
            ->getHandler($source)
            ->fetchAsset($identifier->getId());
    }

    public function transform(Asset $asset, Transformation $transformation): AssetVariation
    {
        return $this
            ->assetVariationGeneratorRegistry
            ->getVariationGenerator($asset->getSource())
            ->generate($asset, $transformation);
    }
}

class_alias(AssetService::class, 'Ibexa\Platform\Connector\Dam\AssetService');
