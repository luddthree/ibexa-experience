<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Form\Attribute\Search\Elasticsearch;

use Ibexa\Contracts\Measurement\Product\Query\Criterion\SimpleMeasurementAttribute;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractAttribute;

/**
 * @extends \Ibexa\Measurement\ProductCatalog\Form\Attribute\Search\Elasticsearch\AbstractMeasurementAttributeVisitor<
 *     \Ibexa\Contracts\Measurement\Product\Query\Criterion\SimpleMeasurementAttribute,
 * >
 */
final class SimpleMeasurementAttributeVisitor extends AbstractMeasurementAttributeVisitor
{
    protected function getCriterionClass(): string
    {
        return SimpleMeasurementAttribute::class;
    }

    protected function getAttributeType(AbstractAttribute $criterion): string
    {
        return 'f';
    }
}
