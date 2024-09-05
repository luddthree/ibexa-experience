<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam\Search;

use Ibexa\Contracts\Connector\Dam\AssetSource;
use Ibexa\Contracts\Connector\Dam\Search\QueryFactory;
use Traversable;

class DefaultQueryFactoryRegistry implements QueryFactoryRegistry
{
    public const GENERIC_FACTORY_INDEX = 'generic';

    /** @var \Ibexa\Contracts\Connector\Dam\Search\QueryFactory[] */
    private $factories;

    public function __construct(Traversable $factories)
    {
        $this->factories = iterator_to_array($factories);
    }

    public function getFactory(AssetSource $source): QueryFactory
    {
        return $this->factories[$source->getSourceIdentifier()] ?? $this->factories[self::GENERIC_FACTORY_INDEX];
    }
}

class_alias(DefaultQueryFactoryRegistry::class, 'Ibexa\Platform\Connector\Dam\Search\DefaultQueryFactoryRegistry');
