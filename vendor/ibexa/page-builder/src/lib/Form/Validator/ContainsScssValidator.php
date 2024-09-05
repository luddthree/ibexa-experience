<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Form\Validator;

use Exception;
use ScssPhp\ScssPhp\Compiler;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ContainsScssValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param \Symfony\Component\Validator\Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        try {
            (new Compiler())->compile(sprintf(
                '[data-ez-block-id="0"] { %s }',
                $value
            ));
        } catch (Exception $exception) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ reason }}', $exception->getMessage())
                ->addViolation();
        }
    }
}

class_alias(ContainsScssValidator::class, 'EzSystems\EzPlatformPageBuilder\Form\Validator\ContainsScssValidator');
