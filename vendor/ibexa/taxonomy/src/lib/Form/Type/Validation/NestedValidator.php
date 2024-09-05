<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Form\Type\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class NestedValidator extends ConstraintValidator
{
    /**
     * @param \Ibexa\Taxonomy\Form\Type\Validation\Constraint\Nested $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if ($value === null) {
            return;
        }

        $value = ($constraint->accessor)($value);

        $validator = $this->context->getValidator()->inContext($this->context);
        $validator->validate($value, $constraint->constraints);
    }
}
