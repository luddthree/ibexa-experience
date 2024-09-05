<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam\Variation;

use Ibexa\Contracts\Connector\Dam\AssetSource;
use Ibexa\Contracts\Connector\Dam\Variation\TransformationFactory;
use Ibexa\Core\Base\Exceptions\NotFoundException;

class DefaultTransformationFactoryRegistry implements TransformationFactoryRegistry
{
    /** @var \Ibexa\Contracts\Connector\Dam\Variation\TransformationFactory[] */
    private $factories = [];

    public function __construct(iterable $factories = [])
    {
        foreach ($factories as $source => $factory) {
            $this->factories[$source] = $factory;
        }
    }

    public function getFactory(AssetSource $source): TransformationFactory
    {
        if (!\array_key_exists($source->getSourceIdentifier(), $this->factories)) {
            throw new NotFoundException('TransformationFactory', $source->getSourceIdentifier());
        }

        return $this->factories[$source->getSourceIdentifier()];
    }
}

class_alias(DefaultTransformationFactoryRegistry::class, 'Ibexa\Platform\Connector\Dam\Variation\DefaultTransformationFactoryRegistry');
