<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Mapper;

use Ibexa\Contracts\FormBuilder\FieldType\Model\Validator;
use Symfony\Component\Validator\Constraint;

interface ValidatorConstraintMapperInterface
{
    /**
     * @return string
     */
    public function getTypeIdentifier(): string;

    /**
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Validator $validator
     *
     * @return \Symfony\Component\Validator\Constraint
     */
    public function map(Validator $validator): Constraint;
}

class_alias(ValidatorConstraintMapperInterface::class, 'EzSystems\EzPlatformFormBuilder\Form\Mapper\ValidatorConstraintMapperInterface');
