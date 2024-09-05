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

class AttributeSelectType extends AbstractType
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
        return 'field_configuration_attribute_select';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['multiple'] ?? false) {
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
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('expanded', false);
    }
}

class_alias(AttributeSelectType::class, 'EzSystems\EzPlatformFormBuilder\Form\Type\FieldAttribute\AttributeSelectType');
