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

class UploadSizeValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $value The value that should be validated
     * @param \Symfony\Component\Validator\Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof UploadSize) {
            throw new UnexpectedTypeException($constraint, UploadSize::class);
        }

        if (empty($value)) {
            return;
        }

        if ($constraint->uploadSize && $value->getSize() > ($constraint->uploadSize * 1024 * 1024)) {
            $this->context->buildViolation($constraint->uploadSizeMessage)
                ->setCode(UploadSize::UPLOAD_SIZE_EXCEEDED_ERROR)
                ->addViolation();
        }
    }
}

class_alias(UploadSizeValidator::class, 'EzSystems\EzPlatformFormBuilder\Form\Validator\Constraints\UploadSizeValidator');
