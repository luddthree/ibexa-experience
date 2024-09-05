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

class RequiredValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param \Symfony\Component\Validator\Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Required) {
            throw new UnexpectedTypeException($constraint, Required::class);
        }

        if ($constraint->required && (empty($value) && !is_numeric($value))) {
            $this->context->buildViolation($constraint->requiredMessage)
                ->setCode(Required::IS_REQUIRED_ERROR)
                ->addViolation();
        }
    }
}

class_alias(RequiredValidator::class, 'EzSystems\EzPlatformFormBuilder\Form\Validator\Constraints\RequiredValidator');
