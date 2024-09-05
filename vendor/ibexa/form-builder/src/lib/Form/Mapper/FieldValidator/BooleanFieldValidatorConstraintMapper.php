<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Mapper\FieldValidator;

use Ibexa\Contracts\FormBuilder\FieldType\Model\Validator;
use Symfony\Component\Validator\Constraint;

class BooleanFieldValidatorConstraintMapper extends GenericFieldValidatorConstraintMapper
{
    /**
     * {@inheritdoc}
     */
    public function map(Validator $validator): Constraint
    {
        return new $this->constraintClass((bool)$validator->getValue());
    }
}

class_alias(BooleanFieldValidatorConstraintMapper::class, 'EzSystems\EzPlatformFormBuilder\Form\Mapper\FieldValidator\BooleanFieldValidatorConstraintMapper');
