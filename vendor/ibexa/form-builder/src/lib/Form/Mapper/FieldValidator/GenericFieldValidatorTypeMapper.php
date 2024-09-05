<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Mapper\FieldValidator;

use Ibexa\FormBuilder\Definition\FieldDefinition;
use Ibexa\FormBuilder\Definition\FieldValidatorDefinition;
use Ibexa\FormBuilder\Form\Mapper\ValidatorConfigurationMapperInterface;
use Symfony\Component\Form\FormBuilderInterface;

class GenericFieldValidatorTypeMapper implements ValidatorConfigurationMapperInterface
{
    /** @var string */
    private $formTypeClass;

    /** @var string */
    private $typeIdentifier;

    /**
     * @param string $formTypeClass
     * @param string $typeIdentifier
     */
    public function __construct(
        string $formTypeClass,
        string $typeIdentifier
    ) {
        $this->formTypeClass = $formTypeClass;
        $this->typeIdentifier = $typeIdentifier;
    }

    /**
     * @return string
     */
    public function getTypeIdentifier(): string
    {
        return $this->typeIdentifier;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $formBuilder
     * @param \Ibexa\FormBuilder\Definition\FieldDefinition $fieldDefinition
     * @param \Ibexa\FormBuilder\Definition\FieldValidatorDefinition $fieldValidatorDefinition
     * @param array $constraints
     *
     * @return \Symfony\Component\Form\FormBuilderInterface
     */
    public function map(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        FieldValidatorDefinition $fieldValidatorDefinition,
        array $constraints = []
    ): FormBuilderInterface {
        return $formBuilder->create(
            'value',
            $this->formTypeClass,
            [
                'constraints' => $constraints,
            ]
        );
    }
}

class_alias(GenericFieldValidatorTypeMapper::class, 'EzSystems\EzPlatformFormBuilder\Form\Mapper\FieldValidator\GenericFieldValidatorTypeMapper');
