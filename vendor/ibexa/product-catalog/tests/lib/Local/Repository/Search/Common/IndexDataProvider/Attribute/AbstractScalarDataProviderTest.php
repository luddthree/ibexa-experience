<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\Search\Common\IndexDataProvider\Attribute;

use Ibexa\Contracts\Core\Search\Field;
use Ibexa\Contracts\Core\Search\FieldType;
use Ibexa\ProductCatalog\Local\Persistence\Values\Attribute;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinition;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\IndexDataProvider\Attribute\AbstractScalarDataProvider;
use PHPUnit\Framework\TestCase;
use Traversable;

/**
 * @covers \Ibexa\ProductCatalog\Local\Repository\Search\Common\IndexDataProvider\Attribute\AbstractScalarDataProvider
 */
final class AbstractScalarDataProviderTest extends TestCase
{
    public function testGetFieldsForAttribute(): void
    {
        $provider = new class($this->createMock(FieldType::class)) extends AbstractScalarDataProvider {
            private FieldType $fieldType;

            public function __construct(FieldType $fieldType)
            {
                $this->fieldType = $fieldType;
            }

            protected function getSearchFieldType(): FieldType
            {
                return $this->fieldType;
            }
        };

        $attributeDefinition = new AttributeDefinition();
        $attributeDefinition->identifier = '<FOO_IDENTIFIER>';
        $attribute = new Attribute(0, 0, '<FOO_DISCRIMINATOR>', '<FOO_VALUE>');

        $searchFields = $provider->getFieldsForAttribute($attributeDefinition, $attribute);
        if ($searchFields instanceof Traversable) {
            $searchFields = iterator_to_array($searchFields, false);
        }

        self::assertCount(1, $searchFields);
        [$searchField] = $searchFields;

        self::assertInstanceOf(Field::class, $searchField);
        self::assertSame('<FOO_VALUE>', $searchField->getValue());
        self::assertSame('product_attribute_<FOO_IDENTIFIER>_value', $searchField->getName());
    }
}
