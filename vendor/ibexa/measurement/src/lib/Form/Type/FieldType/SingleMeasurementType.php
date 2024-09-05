<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\Form\Type\FieldType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SingleMeasurementType extends AbstractType
{
    public function getParent(): string
    {
        return MeasurementType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'ibexa_fieldtype_measurement_single';
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $valueAttr = [];
        if (null !== $options['minimum']) {
            $valueAttr['min'] = $options['minimum'];
        }
        if (null !== $options['maximum']) {
            $valueAttr['max'] = $options['maximum'];
        }

        $builder
            ->add(
                'value',
                NumberType::class,
                [
                    'label' => /** @Desc("Value") */ 'content.field_type.ibexa_measurement.value',
                    'required' => $options['required'],
                    'attr' => $valueAttr,
                ]
            );
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars += [
            'measurement_sign' => $options['sign'],
        ];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'sign' => null,
            'minimum' => null,
            'maximum' => null,
            'defaultValue' => null,
            'translation_domain' => 'ibexa_measurement_fieldtype',
        ]);

        $resolver->setAllowedTypes('sign', ['null', 'string']);
        $resolver->setAllowedTypes('minimum', ['null', 'float']);
        $resolver->setAllowedTypes('maximum', ['null', 'float']);
        $resolver->setAllowedTypes('defaultValue', ['null', 'float']);
    }
}
