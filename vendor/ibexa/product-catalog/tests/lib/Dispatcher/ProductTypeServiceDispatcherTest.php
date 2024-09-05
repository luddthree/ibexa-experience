<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Dispatcher;

use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductType\ProductTypeListInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductType\ProductTypeQuery;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;
use Ibexa\ProductCatalog\Dispatcher\AbstractServiceDispatcher;
use Ibexa\ProductCatalog\Dispatcher\ProductTypeServiceDispatcher;
use Psr\Container\ContainerInterface;

/**
 * @extends \Ibexa\Tests\ProductCatalog\Dispatcher\AbstractServiceDispatcherTest<
 *  \Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface,
 *  \Ibexa\ProductCatalog\Dispatcher\ProductTypeServiceDispatcher
 * >
 */
final class ProductTypeServiceDispatcherTest extends AbstractServiceDispatcherTest
{
    protected function createDispatcherUnderTest(
        ConfigProviderInterface $configProvider,
        ContainerInterface $container
    ): AbstractServiceDispatcher {
        return new ProductTypeServiceDispatcher($configProvider, $container);
    }

    protected function getTargetServiceClass(): string
    {
        return ProductTypeServiceInterface::class;
    }

    public function dataProviderForTestDelegate(): iterable
    {
        yield [
            'getProductType',
            ['foo'],
            $this->createMock(ProductTypeInterface::class),
        ];

        yield [
            'findProductTypes',
            [new ProductTypeQuery()],
            $this->createMock(ProductTypeListInterface::class),
        ];
    }
}
