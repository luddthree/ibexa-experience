<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Form\Attribute\Search\Solr;

use Ibexa\Contracts\Measurement\Product\Query\Criterion\RangeMeasurementAttributeMaximum;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractAttribute;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\FieldNameBuilder\AttributeFieldNameBuilder;

/**
 * @extends \Ibexa\Measurement\ProductCatalog\Form\Attribute\Search\Solr\AbstractMeasurementAttributeVisitor<
 *     \Ibexa\Contracts\Measurement\Product\Query\Criterion\RangeMeasurementAttributeMaximum,
 * >
 */
final class RangeMeasurementAttributeMaximumVisitor extends AbstractMeasurementAttributeVisitor
{
    protected function getCriterionClass(): string
    {
        return RangeMeasurementAttributeMaximum::class;
    }

    protected function getAttributeFieldNameBuilder(AbstractAttribute $criterion): AttributeFieldNameBuilder
    {
        $fieldNameBuilder = parent::getAttributeFieldNameBuilder($criterion);
        $fieldNameBuilder->withField('max_value');

        return $fieldNameBuilder;
    }
}
