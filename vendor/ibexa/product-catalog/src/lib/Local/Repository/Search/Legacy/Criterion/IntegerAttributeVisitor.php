<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Legacy\Criterion;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractAttribute;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\IntegerAttribute;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Attribute\Integer\StorageSchema;

/**
 * @extends \Ibexa\ProductCatalog\Local\Repository\Search\Legacy\Criterion\SingleFieldAttributeVisitor<
 *     \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\IntegerAttribute,
 * >
 */
final class IntegerAttributeVisitor extends SingleFieldAttributeVisitor
{
    protected function getSupportedProductCriterion(): string
    {
        return IntegerAttribute::class;
    }

    protected function getAttributeValueStorageTable(): string
    {
        return StorageSchema::TABLE_NAME;
    }

    protected function getAttributeValueStorageColumn(AbstractAttribute $criterion): string
    {
        return StorageSchema::COLUMN_VALUE;
    }

    protected function getCriterionBindType(AbstractAttribute $criterion): int
    {
        return Type::getType(Types::INTEGER)->getBindingType();
    }
}
