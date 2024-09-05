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
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RangeMeasurementType extends AbstractType
{
    public function getParent(): string
    {
        return MeasurementType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'ibexa_fieldtype_measurement_range';
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
                'measurementRangeMinimumValue',
                NumberType::class,
                [
                    'label' => /** @Desc("Minimum") */ 'content.field_type.ibexa_measurement.range_minimum_value',
                    'required' => $options['required'],
                    'attr' => $valueAttr,
                ]
            )
            ->add(
                'measurementRangeMaximumValue',
                NumberType::class,
                [
                    'label' => /** @Desc("Maximum") */ 'content.field_type.ibexa_measurement.range_maximum_value',
                    'required' => $options['required'],
                    'attr' => $valueAttr,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'minimum' => null,
            'maximum' => null,
            'defaultRangeMinimumValue' => null,
            'defaultRangeMaximumValue' => null,
            'translation_domain' => 'ibexa_measurement_fieldtype',
        ]);

        $resolver->setAllowedTypes('minimum', ['null', 'float']);
        $resolver->setAllowedTypes('maximum', ['null', 'float']);
        $resolver->setAllowedTypes('defaultRangeMinimumValue', ['null', 'float']);
        $resolver->setAllowedTypes('defaultRangeMaximumValue', ['null', 'float']);
    }
}
