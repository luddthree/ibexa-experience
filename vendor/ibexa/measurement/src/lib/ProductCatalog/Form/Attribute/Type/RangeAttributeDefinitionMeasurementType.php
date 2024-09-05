<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Form\Attribute\Type;

use Ibexa\Contracts\Measurement\ConfigResolver\MeasurementTypesInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RangeAttributeDefinitionMeasurementType extends AbstractType
{
    private MeasurementTypesInterface $measurementTypes;

    public function __construct(MeasurementTypesInterface $measurementTypes)
    {
        $this->measurementTypes = $measurementTypes;
    }

    public function getBlockPrefix(): string
    {
        return 'ibexa_attribute_definition_measurement';
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'type',
                ChoiceType::class,
                [
                    'required' => true,
                    'label' => /** @Desc("Measurement Type") */ 'product_catalog.attribute.ibexa_measurement.measurement_type',
                    'choices' => $this->measurementTypes->getTypes(),
                ]
            )
            ->add(
                'unit',
                ChoiceType::class,
                [
                    'required' => true,
                    'label' => /** @Desc("Base Unit") */ 'product_catalog.attribute.ibexa_measurement.measurement_unit',
                    'choices' => $this->measurementTypes->getUnitsByTypes(),
                ]
            )
            ->add(
                'measurementRange',
                AttributeDefinitionMeasurementRangeFieldType::class,
                [
                    'field_name_minimum' => 'minimum',
                    'field_name_maximum' => 'maximum',
                ]
            )
            ->add(
                'measurementDefaultRange',
                AttributeDefinitionMeasurementRangeFieldType::class,
                [
                    'field_name_minimum' => 'defaultRangeMinimumValue',
                    'field_name_maximum' => 'defaultRangeMaximumValue',
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'ibexa_product_catalog_attributes',
            'translation_mode' => false,
        ]);

        $resolver->setAllowedTypes('translation_mode', 'bool');
    }
}
