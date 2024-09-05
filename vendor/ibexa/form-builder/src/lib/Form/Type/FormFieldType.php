<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Type;

use Ibexa\FormBuilder\FieldType\Converter\FormConverter;
use Ibexa\FormBuilder\FieldType\Value;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Form Type representing ezform field type.
 */
class FormFieldType extends AbstractType
{
    /** @var \Ibexa\FormBuilder\FieldType\Converter\FormConverter */
    protected $converter;

    /**
     * @param \Ibexa\FormBuilder\FieldType\Converter\FormConverter $converter
     */
    public function __construct(FormConverter $converter)
    {
        $this->converter = $converter;
    }

    /**
     * @return string {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * @return string {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ezplatform_fieldtype_ezform';
    }

    /**
     * @return string {@inheritdoc}
     */
    public function getParent()
    {
        return HiddenType::class;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(
            $this->getDataTransformer()
        );
    }

    private function getDataTransformer(): CallbackTransformer
    {
        return new CallbackTransformer(
            function (Value $value) {
                $form = $value->getFormValue();

                return $this->converter->encode($form);
            },
            function ($value) {
                $decoded = $this->converter->decode($value);

                return new Value($decoded);
            }
        );
    }
}

class_alias(FormFieldType::class, 'EzSystems\EzPlatformFormBuilder\Form\Type\FormFieldType');
