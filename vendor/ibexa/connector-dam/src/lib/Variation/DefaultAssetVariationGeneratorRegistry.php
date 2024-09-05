<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam\Variation;

use Ibexa\Contracts\Connector\Dam\AssetSource;
use Ibexa\Contracts\Connector\Dam\Variation\AssetVariationGenerator;
use Ibexa\Core\Base\Exceptions\NotFoundException;

final class DefaultAssetVariationGeneratorRegistry implements AssetVariationGeneratorRegistry
{
    /** @var \Ibexa\Contracts\Connector\Dam\Variation\AssetVariationGenerator[] */
    private $generators = [];

    public function __construct(iterable $generators = [])
    {
        foreach ($generators as $source => $generator) {
            $this->generators[$source] = $generator;
        }
    }

    public function getVariationGenerator(AssetSource $source): AssetVariationGenerator
    {
        if (!\array_key_exists($source->getSourceIdentifier(), $this->generators)) {
            throw new NotFoundException('AssetVariationGenerator', $source->getSourceIdentifier());
        }

        return $this->generators[$source->getSourceIdentifier()];
    }
}

class_alias(DefaultAssetVariationGeneratorRegistry::class, 'Ibexa\Platform\Connector\Dam\Variation\DefaultAssetVariationGeneratorRegistry');
