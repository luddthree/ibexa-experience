<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Local\Repository\CatalogFilter\DefinitionProvider\Attribute;

use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractAttribute;
use Ibexa\ProductCatalog\Local\Repository\CatalogFilter\DefinitionProvider\Attribute\FilterDefinitionFactory;
use Ibexa\ProductCatalog\Local\Repository\CatalogFilter\ProductAttribute;
use PHPUnit\Framework\TestCase;

final class FilterDefinitionFactoryTest extends TestCase
{
    private const EXAMPLE_IDENTIFIER = 'product_attribute_foo';
    private const EXAMPLE_CRITERION_CLASS = AbstractAttribute::class;

    public function testCreateFilterDefinition(): void
    {
        $attributeDefinition = $this->createMock(AttributeDefinitionInterface::class);

        $factory = new FilterDefinitionFactory(self::EXAMPLE_CRITERION_CLASS);

        $filterDefinition = $factory->createFilterDefinition(
            $attributeDefinition,
            self::EXAMPLE_IDENTIFIER
        );

        self::assertInstanceOf(ProductAttribute::class, $filterDefinition);
        self::assertEquals(self::EXAMPLE_IDENTIFIER, $filterDefinition->getIdentifier());
        self::assertEquals($attributeDefinition, $filterDefinition->getAttributeDefinition());
    }
}
