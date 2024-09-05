<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Dispatcher;

use Ibexa\Contracts\ProductCatalog\AssetServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Asset\AssetCollectionInterface;
use Ibexa\Contracts\ProductCatalog\Values\Asset\AssetInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;
use Ibexa\ProductCatalog\Dispatcher\AbstractServiceDispatcher;
use Ibexa\ProductCatalog\Dispatcher\AssetsServiceDispatcher;
use Psr\Container\ContainerInterface;

/**
 * @extends \Ibexa\Tests\ProductCatalog\Dispatcher\AbstractServiceDispatcherTest<
 *  \Ibexa\Contracts\ProductCatalog\AssetServiceInterface,
 *  \Ibexa\ProductCatalog\Dispatcher\AssetsServiceDispatcher
 * >
 */
final class AssetsServiceDispatcherTest extends AbstractServiceDispatcherTest
{
    protected function getTargetServiceClass(): string
    {
        return AssetServiceInterface::class;
    }

    protected function createDispatcherUnderTest(
        ConfigProviderInterface $configProvider,
        ContainerInterface $container
    ): AbstractServiceDispatcher {
        return new AssetsServiceDispatcher($configProvider, $container);
    }

    public function dataProviderForTestDelegate(): iterable
    {
        $product = $this->createMock(ProductInterface::class);

        yield [
            'getAsset',
            [$product, 'foo'],
            $this->createMock(AssetInterface::class),
        ];

        yield [
            'findAssets',
            [$product],
            $this->createMock(AssetCollectionInterface::class),
        ];
    }
}
