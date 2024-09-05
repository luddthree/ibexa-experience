<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Connector\Dam;

use Ibexa\Contracts\Connector\Dam\Search\AssetSearchResult;
use Ibexa\Contracts\Connector\Dam\Search\Query;
use Ibexa\Contracts\Connector\Dam\Variation\AssetVariation;
use Ibexa\Contracts\Connector\Dam\Variation\Transformation;

interface AssetService
{
    public function search(Query $query, AssetSource $assetSource, int $offset = 0, int $limit = 20): AssetSearchResult;

    public function get(AssetIdentifier $identifier, AssetSource $source): Asset;

    public function transform(Asset $asset, Transformation $transformation): AssetVariation;
}

class_alias(AssetService::class, 'Ibexa\Platform\Contracts\Connector\Dam\AssetService');
