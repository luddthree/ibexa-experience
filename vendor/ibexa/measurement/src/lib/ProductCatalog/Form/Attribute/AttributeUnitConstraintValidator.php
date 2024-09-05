<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Form\Attribute;

use Ibexa\Contracts\Measurement\MeasurementTypeFactoryInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinitionOptions;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class AttributeUnitConstraintValidator extends ConstraintValidator
{
    private MeasurementTypeFactoryInterface $measurementTypeFactory;

    public function __construct(MeasurementTypeFactoryInterface $measurementTypeFactory)
    {
        $this->measurementTypeFactory = $measurementTypeFactory;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof AttributeUnitConstraint) {
            throw new UnexpectedTypeException($constraint, AttributeUnitConstraint::class);
        }

        if (null === $value) {
            return;
        }

        if (!$value instanceof AttributeDefinitionOptions) {
            throw new UnexpectedValueException($value, AttributeDefinitionOptions::class);
        }

        /** @var string|null $type */
        $type = $value->get('type');

        if ($type === null) {
            return;
        }

        if (!$this->measurementTypeFactory->hasType($type)) {
            $this->context->buildViolation('Type does not exist')
                ->atPath('[type]')
                ->addViolation();

            return;
        }

        $measurementType = $this->measurementTypeFactory->buildType($type);

        /** @var string|null $unit */
        $unit = $value->get('unit');
        if ($unit === null) {
            return;
        }

        if (!$measurementType->hasUnit($unit)) {
            $this->context->buildViolation('Unit does not exist in type')
                ->atPath('[unit]')
                ->addViolation();
        }
    }
}
