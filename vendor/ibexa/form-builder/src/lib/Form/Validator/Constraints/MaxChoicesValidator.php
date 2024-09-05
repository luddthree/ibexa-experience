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

class MaxChoicesValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param \Symfony\Component\Validator\Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof MaxChoices) {
            throw new UnexpectedTypeException($constraint, MaxChoices::class);
        }

        if ($constraint->maxChoices && !empty($value) && \count($value) > $constraint->maxChoices) {
            $this->context->buildViolation($constraint->maxChoicesMessage)
                ->setCode(MaxChoices::MAX_CHOICES_EXCEEDED_ERROR)
                ->addViolation();
        }
    }
}

class_alias(MaxChoicesValidator::class, 'EzSystems\EzPlatformFormBuilder\Form\Validator\Constraints\MaxChoicesValidator');
