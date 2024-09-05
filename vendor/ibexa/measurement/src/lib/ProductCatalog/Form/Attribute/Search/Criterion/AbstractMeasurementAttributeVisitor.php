<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Form\Attribute\Search\Criterion;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractAttribute;
use Ibexa\Measurement\UnitConverter\UnitConverterDispatcherInterface;
use Ibexa\ProductCatalog\Local\Repository\Search\Legacy\Criterion\SingleFieldAttributeVisitor;

/**
 * @template T of \Ibexa\Contracts\Measurement\Product\Query\Criterion\AbstractMeasurementAttribute
 *
 * @extends \Ibexa\ProductCatalog\Local\Repository\Search\Legacy\Criterion\SingleFieldAttributeVisitor<
 *     T,
 * >
 */
abstract class AbstractMeasurementAttributeVisitor extends SingleFieldAttributeVisitor
{
    private UnitConverterDispatcherInterface $unitConverter;

    public function __construct(
        Connection $connection,
        UnitConverterDispatcherInterface $unitConverter
    ) {
        parent::__construct($connection);
        $this->unitConverter = $unitConverter;
    }

    final protected function getCriterionBindType(AbstractAttribute $criterion): int
    {
        return Type::getType(Types::FLOAT)->getBindingType();
    }

    final protected function getCriterionValue(AbstractAttribute $criterion): ?float
    {
        $criterionValue = $criterion->getValue();

        $criterionUnit = $criterionValue->getUnit();
        if ($criterionUnit->isBaseUnit()) {
            return $criterionValue->getValue();
        }

        $measurement = $criterionValue->getMeasurement();
        $baseUnit = $measurement->getBaseUnit();
        $baseValue = $this->unitConverter->convert($criterionValue, $baseUnit);

        return $baseValue->getValue();
    }
}
