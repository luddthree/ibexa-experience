<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Mapper\FieldValidator;

use Ibexa\Contracts\FormBuilder\FieldType\Model\Validator;
use Ibexa\FormBuilder\Form\Mapper\ValidatorConstraintMapperInterface;
use Symfony\Component\Validator\Constraint;

class GenericFieldValidatorConstraintMapper implements ValidatorConstraintMapperInterface
{
    /** @var string */
    private $typeIdentifier;

    /** @var string */
    protected $constraintClass;

    /**
     * @param string $constraintClass
     * @param string $typeIdentifier
     */
    public function __construct(
        string $constraintClass,
        string $typeIdentifier
    ) {
        $this->typeIdentifier = $typeIdentifier;
        $this->constraintClass = $constraintClass;
    }

    /**
     * @return string
     */
    public function getTypeIdentifier(): string
    {
        return $this->typeIdentifier;
    }

    /**
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Validator $validator
     *
     * @return \Symfony\Component\Validator\Constraint
     */
    public function map(Validator $validator): Constraint
    {
        return new $this->constraintClass($validator->getValue());
    }
}

class_alias(GenericFieldValidatorConstraintMapper::class, 'EzSystems\EzPlatformFormBuilder\Form\Mapper\FieldValidator\GenericFieldValidatorConstraintMapper');
