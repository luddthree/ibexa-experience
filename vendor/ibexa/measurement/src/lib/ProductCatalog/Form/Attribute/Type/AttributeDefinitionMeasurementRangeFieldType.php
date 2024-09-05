<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Form\Attribute\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form Type representing attribute definition measurement range field type.
 */
class AttributeDefinitionMeasurementRangeFieldType extends AbstractType
{
    public function getBlockPrefix(): string
    {
        return 'ibexa_fieldtype_measurement_range';
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $translationKey = 'product_catalog.attribute.ibexa_measurement.';

        assert(is_string($options['field_name_minimum']));
        assert(is_string($options['field_name_maximum']));
        $builder
            ->add(
                $options['field_name_minimum'],
                NumberType::class,
                [
                    'required' => false,
                    'label' => $translationKey . $options['field_name_minimum'],
                ]
            )
            ->add(
                $options['field_name_maximum'],
                NumberType::class,
                [
                    'required' => false,
                    'label' => $translationKey . $options['field_name_maximum'],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'ibexa_product_catalog_attributes',
        ]);

        $resolver->setRequired(['field_name_minimum', 'field_name_maximum']);
    }
}
