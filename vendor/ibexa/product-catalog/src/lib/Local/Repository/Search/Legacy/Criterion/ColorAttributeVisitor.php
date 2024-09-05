<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Legacy\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractAttribute;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ColorAttribute;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Attribute\Selection\StorageSchema;

/**
 * @extends \Ibexa\ProductCatalog\Local\Repository\Search\Legacy\Criterion\SingleFieldAttributeVisitor<
 *     \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ColorAttribute,
 * >
 */
final class ColorAttributeVisitor extends SingleFieldAttributeVisitor
{
    protected function getSupportedProductCriterion(): string
    {
        return ColorAttribute::class;
    }

    protected function getAttributeValueStorageTable(): string
    {
        return StorageSchema::TABLE_NAME;
    }

    protected function getAttributeValueStorageColumn(AbstractAttribute $criterion): string
    {
        return StorageSchema::COLUMN_VALUE;
    }
}
