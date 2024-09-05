<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Form\Attribute\Search\Elasticsearch;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractAttribute;
use Ibexa\Measurement\UnitConverter\UnitConverterDispatcherInterface;
use Ibexa\ProductCatalog\Local\Repository\Search\ElasticSearch\Criterion\AbstractAttributeVisitor;

/**
 * @template TCriterion of \Ibexa\Contracts\Measurement\Product\Query\Criterion\AbstractMeasurementAttribute
 *
 * @extends \Ibexa\ProductCatalog\Local\Repository\Search\ElasticSearch\Criterion\AbstractAttributeVisitor<
 *     TCriterion,
 * >
 */
abstract class AbstractMeasurementAttributeVisitor extends AbstractAttributeVisitor
{
    private UnitConverterDispatcherInterface $unitConverterDispatcher;

    public function __construct(UnitConverterDispatcherInterface $unitConverterDispatcher)
    {
        $this->unitConverterDispatcher = $unitConverterDispatcher;
    }

    protected function getAttributeType(AbstractAttribute $criterion): string
    {
        return 'f';
    }

    protected function getCriterionValue(AbstractAttribute $criterion): float
    {
        $measurementValue = $criterion->getValue();

        if (!$measurementValue->getUnit()->isBaseUnit()) {
            $measurementValue = $this->unitConverterDispatcher->convert(
                $measurementValue,
                $measurementValue->getMeasurement()->getBaseUnit()
            );
        }

        // Elasticsearch precision for data input for float-type fields restricts the available precision
        // See https://www.elastic.co/guide/en/elasticsearch/reference/7.17/number.html#_which_type_should_i_use
        return round($measurementValue->getValue(), 6);
    }
}
