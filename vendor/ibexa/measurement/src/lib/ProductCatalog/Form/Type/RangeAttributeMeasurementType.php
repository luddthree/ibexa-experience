<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Form\Type;

use Ibexa\Contracts\Measurement\MeasurementServiceInterface;
use Ibexa\Measurement\Form\Type\MeasurementType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RangeAttributeMeasurementType extends AbstractType
{
    private MeasurementServiceInterface $measurementService;

    public function __construct(MeasurementServiceInterface $measurementService)
    {
        $this->measurementService = $measurementService;
    }

    public function getBlockPrefix(): string
    {
        return 'ibexa_fieldtype_measurement_range';
    }

    public function getParent(): string
    {
        return MeasurementType::class;
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

        $builder->addModelTransformer(new RangeAttributeModelTransformer($this->measurementService));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'ibexa_measurement_fieldtype',
            'minimum' => null,
            'maximum' => null,
            'defaultRangeMinimumValue' => null,
            'defaultRangeMaximumValue' => null,
        ]);

        $resolver->setAllowedTypes('minimum', ['null', 'float']);
        $resolver->setAllowedTypes('maximum', ['null', 'float']);
        $resolver->setAllowedTypes('defaultRangeMinimumValue', ['null', 'float']);
        $resolver->setAllowedTypes('defaultRangeMaximumValue', ['null', 'float']);
    }
}
