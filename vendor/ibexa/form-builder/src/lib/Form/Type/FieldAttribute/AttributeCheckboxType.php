<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Type\FieldAttribute;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class AttributeCheckboxType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new class() implements DataTransformerInterface {
            public function transform($value): bool
            {
                return (bool)$value;
            }

            public function reverseTransform($value): bool
            {
                return (bool)$value;
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return CheckboxType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'field_configuration_attribute_checkbox';
    }
}

class_alias(AttributeCheckboxType::class, 'EzSystems\EzPlatformFormBuilder\Form\Type\FieldAttribute\AttributeCheckboxType');
