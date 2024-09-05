<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam\Variation;

use Ibexa\Contracts\Connector\Dam\Asset;
use Ibexa\Contracts\Connector\Dam\AssetUri;
use Ibexa\Contracts\Connector\Dam\Variation\AssetVariation;
use Ibexa\Contracts\Connector\Dam\Variation\AssetVariationGenerator;
use Ibexa\Contracts\Connector\Dam\Variation\Transformation;

class URLBasedVariationGenerator implements AssetVariationGenerator
{
    public function generate(Asset $asset, Transformation $transformation): AssetVariation
    {
        $location = $asset->getAssetUri()->getPath();

        $transformationParamQuery = http_build_query($transformation->getTransformationProperties());

        $concatenationSign = '?';
        if (parse_url($location, PHP_URL_QUERY)) {
            $concatenationSign = '&';
        }

        return new AssetVariation(
            $asset,
            new AssetUri($location . $concatenationSign . $transformationParamQuery),
            $transformation
        );
    }
}

class_alias(URLBasedVariationGenerator::class, 'Ibexa\Platform\Connector\Dam\Variation\URLBasedVariationGenerator');
