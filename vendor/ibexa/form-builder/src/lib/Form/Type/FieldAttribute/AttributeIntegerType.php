<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Type\FieldAttribute;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class AttributeIntegerType extends AbstractType
{
    /**
     * @return string|null
     */
    public function getParent()
    {
        return IntegerType::class;
    }

    /**
     * @return string
     */
    public function getFieldPrefix()
    {
        return 'field_configuration_attribute_integer';
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
                    return (int)$input;
                },
                static function ($input) {
                    return (int)$input;
                }
            )
        );
    }
}

class_alias(AttributeIntegerType::class, 'EzSystems\EzPlatformFormBuilder\Form\Type\FieldAttribute\AttributeIntegerType');
