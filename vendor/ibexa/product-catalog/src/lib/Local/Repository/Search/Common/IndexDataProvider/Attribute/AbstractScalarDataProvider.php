<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Common\IndexDataProvider\Attribute;

use Ibexa\Contracts\Core\Search;
use Ibexa\Contracts\Core\Search\FieldType;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\IndexDataProviderInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\Attribute;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinition;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\FieldNameBuilder\AttributeFieldNameBuilder;

/**
 * @template TValue
 *
 * @implements \Ibexa\Contracts\ProductCatalog\Local\Attribute\IndexDataProviderInterface<TValue>
 */
abstract class AbstractScalarDataProvider implements IndexDataProviderInterface
{
    final public function getFieldsForAttribute(AttributeDefinition $attributeDefinition, Attribute $attribute): iterable
    {
        $value = $attribute->getValue();
        $fieldNameBuilder = new AttributeFieldNameBuilder($attributeDefinition->identifier);

        if ($value === null) {
            $fieldNameBuilder->withIsNull();
            $fieldName = $fieldNameBuilder->build();

            return [
                new Search\Field(
                    $fieldName,
                    true,
                    new Search\FieldType\BooleanField(),
                ),
            ];
        }

        $fieldNameBuilder->withField('value');

        return [
            new Search\Field(
                $fieldNameBuilder->build(),
                $value,
                $this->getSearchFieldType(),
            ),
        ];
    }

    abstract protected function getSearchFieldType(): FieldType;
}
