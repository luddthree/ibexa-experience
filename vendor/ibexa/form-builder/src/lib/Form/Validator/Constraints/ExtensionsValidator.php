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

class ExtensionsValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $value The value that should be validated
     * @param \Symfony\Component\Validator\Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Extensions) {
            throw new UnexpectedTypeException($constraint, Extensions::class);
        }

        if (empty($value)) {
            return;
        }

        $fileExtension = strtolower($value->getClientOriginalExtension());
        if (!empty($constraint->extensions) && !\in_array($fileExtension, $constraint->extensions)) {
            $this->context->buildViolation($constraint->extensionsMessage)
                ->setCode(Extensions::EXTENSION_NOT_ALLOWED_ERROR)
                ->addViolation();
        }
    }
}

class_alias(ExtensionsValidator::class, 'EzSystems\EzPlatformFormBuilder\Form\Validator\Constraints\ExtensionsValidator');
