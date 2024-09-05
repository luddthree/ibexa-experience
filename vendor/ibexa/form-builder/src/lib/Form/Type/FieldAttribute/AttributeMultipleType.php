<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Type\FieldAttribute;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AttributeMultipleType extends AbstractType
{
    /**
     * @return string|null
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * @return string
     */
    public function getFieldPrefix()
    {
        return 'field_configuration_attribute_multiple';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(
            new CallbackTransformer(
                static function ($input) {
                    return explode(',', $input ?? '');
                },
                static function ($input) {
                    return implode(',', $input ?? []);
                }
            )
        );
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('multiple', true);
        $resolver->setDefault('expanded', true);
    }
}

class_alias(AttributeMultipleType::class, 'EzSystems\EzPlatformFormBuilder\Form\Type\FieldAttribute\AttributeMultipleType');
