<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class MinChoicesValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param \Symfony\Component\Validator\Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof MinChoices) {
            throw new UnexpectedTypeException($constraint, MinChoices::class);
        }

        if ($constraint->minChoices && !empty($value) && \count($value) < $constraint->minChoices) {
            $this->context->buildViolation($constraint->minChoicesMessage)
                ->setCode(MinChoices::MIN_CHOICES_NOT_REACHED_ERROR)
                ->addViolation();
        }
    }
}

class_alias(MinChoicesValidator::class, 'EzSystems\EzPlatformFormBuilder\Form\Validator\Constraints\MinChoicesValidator');
