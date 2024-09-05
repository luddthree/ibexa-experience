<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Mapper;

use Ibexa\FormBuilder\Definition\FieldDefinition;
use Ibexa\FormBuilder\Definition\FieldValidatorDefinition;
use Symfony\Component\Form\FormBuilderInterface;

interface ValidatorConfigurationMapperInterface
{
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
    ): FormBuilderInterface;

    /**
     * @return string
     */
    public function getTypeIdentifier(): string;
}

class_alias(ValidatorConfigurationMapperInterface::class, 'EzSystems\EzPlatformFormBuilder\Form\Mapper\ValidatorConfigurationMapperInterface');
