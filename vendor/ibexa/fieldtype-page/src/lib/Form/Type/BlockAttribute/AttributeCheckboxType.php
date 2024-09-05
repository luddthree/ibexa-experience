<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\Form\Type\BlockAttribute;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AttributeCheckboxType extends AbstractType
{
    public function getParent(): string
    {
        return CheckboxType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new AttributeCheckboxTransformer());
    }

    public function getBlockPrefix(): string
    {
        return 'block_configuration_attribute_checkbox';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('required', false);
    }
}
