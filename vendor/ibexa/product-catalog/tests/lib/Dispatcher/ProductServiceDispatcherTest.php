<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Dispatcher;

use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\LanguageSettings;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductListInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductVariantListInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductVariantQuery;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;
use Ibexa\ProductCatalog\Dispatcher\AbstractServiceDispatcher;
use Ibexa\ProductCatalog\Dispatcher\ProductServiceDispatcher;
use Psr\Container\ContainerInterface;

/**
 * @extends \Ibexa\Tests\ProductCatalog\Dispatcher\AbstractServiceDispatcherTest<
 *  \Ibexa\Contracts\ProductCatalog\ProductServiceInterface,
 *  \Ibexa\ProductCatalog\Dispatcher\ProductServiceDispatcher
 * >
 */
final class ProductServiceDispatcherTest extends AbstractServiceDispatcherTest
{
    protected function createDispatcherUnderTest(
        ConfigProviderInterface $configProvider,
        ContainerInterface $container
    ): AbstractServiceDispatcher {
        return new ProductServiceDispatcher($configProvider, $container);
    }

    protected function getTargetServiceClass(): string
    {
        return ProductServiceInterface::class;
    }

    public function dataProviderForTestDelegate(): iterable
    {
        yield [
            'getProduct',
            ['0001', new LanguageSettings()],
            $this->createMock(ProductInterface::class),
        ];

        yield [
            'findProducts',
            [
                new ProductQuery(),
                new LanguageSettings(),
            ],
            $this->createMock(ProductListInterface::class),
        ];

        yield [
            'findProductVariants',
            [
                $this->createMock(ProductInterface::class),
                new ProductVariantQuery(),
            ],
            $this->createMock(ProductVariantListInterface::class),
        ];
    }
}
