<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Type;

use Ibexa\FormBuilder\Definition\FieldDefinition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FieldValidatorsConfigurationType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public const LABEL_PREFIX = 'validation.';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \Ibexa\FormBuilder\Definition\FieldDefinition $fieldDefinition */
        $fieldDefinition = $options['field_definition'];

        foreach ($fieldDefinition->getValidators() as $validatorDefinition) {
            $builder->add(
                $builder->create($validatorDefinition->getIdentifier(), FieldValidatorConfigurationType::class, [
                    'label' => self::LABEL_PREFIX . $validatorDefinition->getIdentifier(),
                    'field_definition' => $fieldDefinition,
                    'field_validator_definition' => $validatorDefinition,
                ])
            );
        }
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['field_definition'])
            ->setAllowedTypes('field_definition', FieldDefinition::class);
    }
}

class_alias(FieldValidatorsConfigurationType::class, 'EzSystems\EzPlatformFormBuilder\Form\Type\FieldValidatorsConfigurationType');
