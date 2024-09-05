<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Form\Attribute\Search\Solr;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractAttribute;
use Ibexa\Measurement\UnitConverter\UnitConverterDispatcherInterface;
use Ibexa\ProductCatalog\Local\Repository\Search\Solr\Criterion\AbstractAttributeVisitor;

/**
 * @template TCriterion of \Ibexa\Contracts\Measurement\Product\Query\Criterion\AbstractMeasurementAttribute
 *
 * @extends \Ibexa\ProductCatalog\Local\Repository\Search\Solr\Criterion\AbstractAttributeVisitor<
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

        return $measurementValue->getValue();
    }
}
