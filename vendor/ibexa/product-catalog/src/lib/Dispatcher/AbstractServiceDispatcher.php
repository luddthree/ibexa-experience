<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Dispatcher;

use Ibexa\ProductCatalog\Config\ConfigProviderInterface;
use Psr\Container\ContainerInterface;

/**
 * @template T
 */
abstract class AbstractServiceDispatcher
{
    protected ConfigProviderInterface $configProvider;

    protected ContainerInterface $locator;

    public function __construct(ConfigProviderInterface $configProvider, ContainerInterface $locator)
    {
        $this->configProvider = $configProvider;
        $this->locator = $locator;
    }

    /**
     * @return T
     */
    protected function dispatch()
    {
        return $this->locator->get($this->configProvider->getEngineType());
    }
}
