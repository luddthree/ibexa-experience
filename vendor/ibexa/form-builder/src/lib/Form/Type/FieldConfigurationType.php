<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Type;

use Ibexa\Contracts\FormBuilder\FieldType\Model\Field;
use Ibexa\FormBuilder\Definition\FieldDefinition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class FieldConfigurationType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', HiddenType::class)
            ->add('identifier', HiddenType::class)
            ->add('name', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('attributes', FieldAttributesConfigurationType::class, [
                'label' => false,
                'field_definition' => $options['field_definition'],
            ])
            ->add('validators', FieldValidatorsConfigurationType::class, [
                'label' => false,
                'required' => false,
                'field_definition' => $options['field_definition'],
            ])
            ->add('configure', SubmitType::class);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => Field::class,
                'translation_domain' => 'ibexa_form_builder_field_config',
            ])
            ->setRequired(['field_definition'])
            ->setAllowedTypes('field_definition', FieldDefinition::class);
    }
}

class_alias(FieldConfigurationType::class, 'EzSystems\EzPlatformFormBuilder\Form\Type\FieldConfigurationType');
