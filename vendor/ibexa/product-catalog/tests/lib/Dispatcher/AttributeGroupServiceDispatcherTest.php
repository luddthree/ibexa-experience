<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Dispatcher;

use Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupListInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupQuery;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;
use Ibexa\ProductCatalog\Dispatcher\AbstractServiceDispatcher;
use Ibexa\ProductCatalog\Dispatcher\AttributeGroupServiceDispatcher;
use Psr\Container\ContainerInterface;

/**
 * @extends \Ibexa\Tests\ProductCatalog\Dispatcher\AbstractServiceDispatcherTest<
 *  \Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface,
 *  \Ibexa\ProductCatalog\Dispatcher\AttributeGroupServiceDispatcher
 * >
 */
final class AttributeGroupServiceDispatcherTest extends AbstractServiceDispatcherTest
{
    protected function createDispatcherUnderTest(
        ConfigProviderInterface $configProvider,
        ContainerInterface $container
    ): AbstractServiceDispatcher {
        return new AttributeGroupServiceDispatcher($configProvider, $container);
    }

    protected function getTargetServiceClass(): string
    {
        return AttributeGroupServiceInterface::class;
    }

    public function dataProviderForTestDelegate(): iterable
    {
        yield [
            'getAttributeGroup',
            ['foo'],
            $this->createMock(AttributeGroupInterface::class),
        ];

        yield [
            'findAttributeGroups',
            [new AttributeGroupQuery()],
            $this->createMock(AttributeGroupListInterface::class),
        ];
    }
}
