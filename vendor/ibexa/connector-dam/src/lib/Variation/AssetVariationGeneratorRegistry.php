<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam\Variation;

use Ibexa\Contracts\Connector\Dam\AssetSource;
use Ibexa\Contracts\Connector\Dam\Variation\AssetVariationGenerator;

interface AssetVariationGeneratorRegistry
{
    /**
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    public function getVariationGenerator(AssetSource $source): AssetVariationGenerator;
}

class_alias(AssetVariationGeneratorRegistry::class, 'Ibexa\Platform\Connector\Dam\Variation\AssetVariationGeneratorRegistry');
