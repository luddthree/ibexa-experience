<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Form\Attribute\Search\Criterion;

use Ibexa\Contracts\Measurement\Product\Query\Criterion\SimpleMeasurementAttribute;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractAttribute;
use Ibexa\Measurement\ProductCatalog\Local\Attribute\SingleMeasurementStorageDefinition;

/**
 * @extends \Ibexa\Measurement\ProductCatalog\Form\Attribute\Search\Criterion\AbstractMeasurementAttributeVisitor<
 *     \Ibexa\Contracts\Measurement\Product\Query\Criterion\SimpleMeasurementAttribute,
 * >
 */
final class SimpleMeasurementAttributeVisitor extends AbstractMeasurementAttributeVisitor
{
    protected function getSupportedProductCriterion(): string
    {
        return SimpleMeasurementAttribute::class;
    }

    protected function getAttributeValueStorageTable(): string
    {
        return SingleMeasurementStorageDefinition::TABLE_NAME;
    }

    protected function getAttributeValueStorageColumn(AbstractAttribute $criterion): string
    {
        return SingleMeasurementStorageDefinition::COLUMN_BASE_VALUE;
    }
}
