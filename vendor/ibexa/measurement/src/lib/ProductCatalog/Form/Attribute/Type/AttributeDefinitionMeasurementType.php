<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Form\Attribute\Type;

use Ibexa\Contracts\Measurement\ConfigResolver\MeasurementTypesInterface;
use Ibexa\Contracts\Measurement\Value\Definition\Sign;
use Ibexa\Measurement\FieldType\MeasurementType;
use JMS\TranslationBundle\Annotation\Desc;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AttributeDefinitionMeasurementType extends AbstractType implements TranslationContainerInterface
{
    private const TRANSLATION_DOMAIN = 'ibexa_product_catalog_attributes';

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
                'inputType',
                ChoiceType::class,
                [
                    'required' => true,
                    'label' => /** @Desc("Input Type") */ 'product_catalog.attribute.ibexa_measurement.measurement_input_type',
                    'choices' => [
                        /** @Desc("Simple Input") */
                        'product_catalog.attribute.ibexa_measurement.measurement_input_type.value.simple' => MeasurementType::INPUT_TYPE_SIMPLE_INPUT,
                        /** @Desc("Range") */
                        'product_catalog.attribute.ibexa_measurement.measurement_input_type.value.range' => MeasurementType::INPUT_TYPE_RANGE,
                    ],
                ]
            )
            ->add(
                'sign',
                ChoiceType::class,
                [
                    'required' => true,
                    'label' => /** @Desc("Sign") */ 'product_catalog.attribute.ibexa_measurement.measurement_sign',
                    'choices' => [
                        /** @Desc("none") */
                        'product_catalog.attribute.ibexa_measurement.measurement_sign.value.none' => Sign::SIGN_NONE,
                        /** @Desc("> greater than") */
                        'product_catalog.attribute.ibexa_measurement.measurement_sign.value.gt' => Sign::SIGN_GT,
                        /** @Desc("< less than") */
                        'product_catalog.attribute.ibexa_measurement.measurement_sign.value.lt' => Sign::SIGN_LT,
                        /** @Desc("≥ greater than or equal to") */
                        'product_catalog.attribute.ibexa_measurement.measurement_sign.value.gte' => Sign::SIGN_GTE,
                        /** @Desc("≤ less than or equal to") */
                        'product_catalog.attribute.ibexa_measurement.measurement_sign.value.lte' => Sign::SIGN_LTE,
                        /** @Desc("± plus/minus") */
                        'product_catalog.attribute.ibexa_measurement.measurement_sign.value.pm' => Sign::SIGN_PM,
                    ],
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
                'defaultValue',
                NumberType::class,
                [
                    'required' => false,
                    'label' => /** @Desc("Default value") */ 'product_catalog.attribute.ibexa_measurement.default_value',
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

    public static function getTranslationMessages(): array
    {
        return [
            Message::create('product_catalog.attribute.ibexa_measurement.minimum', self::TRANSLATION_DOMAIN)
                ->setDesc('Minimum Value'),
            Message::create('product_catalog.attribute.ibexa_measurement.maximum', self::TRANSLATION_DOMAIN)
                ->setDesc('Maximum Value'),
            Message::create('product_catalog.attribute.ibexa_measurement.defaultRangeMinimumValue', self::TRANSLATION_DOMAIN)
                ->setDesc('Default Value'),
        ];
    }
}
