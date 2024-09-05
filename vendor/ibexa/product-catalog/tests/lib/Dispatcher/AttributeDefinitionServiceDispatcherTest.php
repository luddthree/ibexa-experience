<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Dispatcher;

use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionListInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionQuery;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;
use Ibexa\ProductCatalog\Dispatcher\AbstractServiceDispatcher;
use Ibexa\ProductCatalog\Dispatcher\AttributeDefinitionServiceDispatcher;
use Psr\Container\ContainerInterface;

/**
 * @extends \Ibexa\Tests\ProductCatalog\Dispatcher\AbstractServiceDispatcherTest<
 *  \Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface,
 *  \Ibexa\ProductCatalog\Dispatcher\AttributeDefinitionServiceDispatcher
 * >
 */
final class AttributeDefinitionServiceDispatcherTest extends AbstractServiceDispatcherTest
{
    protected function getTargetServiceClass(): string
    {
        return AttributeDefinitionServiceInterface::class;
    }

    protected function createDispatcherUnderTest(
        ConfigProviderInterface $configProvider,
        ContainerInterface $container
    ): AbstractServiceDispatcher {
        return new AttributeDefinitionServiceDispatcher($configProvider, $container);
    }

    public function dataProviderForTestDelegate(): iterable
    {
        yield [
            'getAttributeDefinition',
            ['foo'],
            $this->createMock(AttributeDefinitionInterface::class),
        ];

        yield [
            'findAttributesDefinitions',
            [new AttributeDefinitionQuery()],
            $this->createMock(AttributeDefinitionListInterface::class),
        ];
    }
}
