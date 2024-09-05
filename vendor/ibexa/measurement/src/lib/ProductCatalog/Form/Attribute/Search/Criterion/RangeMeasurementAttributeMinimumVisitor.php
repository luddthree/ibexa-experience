<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Form\Attribute\Search\Criterion;

use Ibexa\Contracts\Measurement\Product\Query\Criterion\RangeMeasurementAttributeMinimum;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractAttribute;
use Ibexa\Measurement\ProductCatalog\Local\Attribute\RangeMeasurementStorageDefinition;

/**
 * @extends \Ibexa\Measurement\ProductCatalog\Form\Attribute\Search\Criterion\AbstractMeasurementAttributeVisitor<
 *     \Ibexa\Contracts\Measurement\Product\Query\Criterion\RangeMeasurementAttributeMinimum,
 * >
 */
final class RangeMeasurementAttributeMinimumVisitor extends AbstractMeasurementAttributeVisitor
{
    protected function getSupportedProductCriterion(): string
    {
        return RangeMeasurementAttributeMinimum::class;
    }

    protected function getAttributeValueStorageTable(): string
    {
        return RangeMeasurementStorageDefinition::TABLE_NAME;
    }

    protected function getAttributeValueStorageColumn(AbstractAttribute $criterion): string
    {
        return RangeMeasurementStorageDefinition::COLUMN_BASE_MIN_VALUE;
    }
}
