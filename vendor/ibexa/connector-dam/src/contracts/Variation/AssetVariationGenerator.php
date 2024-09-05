<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Connector\Dam\Variation;

use Ibexa\Contracts\Connector\Dam\Asset;

interface AssetVariationGenerator
{
    public function generate(Asset $asset, Transformation $transformation): AssetVariation;
}

class_alias(AssetVariationGenerator::class, 'Ibexa\Platform\Contracts\Connector\Dam\Variation\AssetVariationGenerator');
