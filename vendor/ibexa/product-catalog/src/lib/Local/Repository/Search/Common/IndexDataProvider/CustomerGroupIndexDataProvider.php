<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Common\IndexDataProvider;

use Ibexa\Contracts\Core\Persistence\Content as SPIContent;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Search;
use Ibexa\ProductCatalog\FieldType\CustomerGroup\Type;

/**
 * @internal
 */
final class CustomerGroupIndexDataProvider extends AbstractFieldTypeIndexDataProvider
{
    protected function doGetSearchData(SPIContent $content, Field $field): array
    {
        if (!isset($field->value->externalData[Type::FIELD_ID_KEY])) {
            return [];
        }

        return [
            new Search\Field(
                'customer_group_id',
                $field->value->externalData[Type::FIELD_ID_KEY],
                new Search\FieldType\IdentifierField(['raw' => true])
            ),
        ];
    }

    protected function getFieldTypeIdentifier(): string
    {
        return Type::FIELD_TYPE_IDENTIFIER;
    }
}
