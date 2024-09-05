<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\Form\Type;

use Ibexa\Contracts\Measurement\ConfigResolver\MeasurementTypesInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class MeasurementType extends AbstractType
{
    private MeasurementTypesInterface $measurementTypes;

    public function __construct(
        MeasurementTypesInterface $measurementTypes
    ) {
        $this->measurementTypes = $measurementTypes;
    }

    public function getBlockPrefix(): string
    {
        return 'ibexa_fieldtype_measurement';
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'measurementType',
                HiddenType::class,
                [
                    'data' => $options['measurementType'],
                    'attr' => [
                        'readonly' => true,
                    ],
                ]
            );

        $builder
            ->add(
                'measurementUnit',
                ChoiceType::class,
                [
                    'label' => /** @Desc("Measurement Unit") */ 'field_definition.ibexa_measurement.measurement_unit',
                    'required' => true,
                    'choices' => $this->measurementTypes->getUnitsByType($options['measurementType']),
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'ibexa_measurement_fieldtype',
        ]);

        $resolver->setRequired(['measurementType', 'measurementUnit']);

        $resolver->setAllowedTypes('measurementType', 'string');
        $resolver->setAllowedTypes('measurementUnit', 'string');
    }
}
