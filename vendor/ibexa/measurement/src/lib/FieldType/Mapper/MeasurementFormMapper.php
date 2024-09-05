<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\FieldType\Mapper;

use Ibexa\AdminUi\FieldType\FieldDefinitionFormMapperInterface;
use Ibexa\AdminUi\Form\Data\FieldDefinitionData;
use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Ibexa\Contracts\ContentForms\FieldType\FieldValueFormMapperInterface;
use Ibexa\Contracts\Measurement\ConfigResolver\MeasurementTypesInterface;
use Ibexa\Measurement\FieldType\MeasurementType;
use Ibexa\Measurement\Form\Type\FieldType\RangeMeasurementType;
use Ibexa\Measurement\Form\Type\FieldType\SingleMeasurementType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * FormMapper for ibexa_measurement FieldType.
 */
class MeasurementFormMapper implements FieldDefinitionFormMapperInterface, FieldValueFormMapperInterface
{
    private MeasurementTypesInterface $measurementTypes;

    public function __construct(
        MeasurementTypesInterface $measurementTypes
    ) {
        $this->measurementTypes = $measurementTypes;
    }

    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data): void
    {
        $isTranslation = $data->contentTypeData->languageCode !== $data->contentTypeData->mainLanguageCode;
        $types = $this->measurementTypes->getTypes();

        $validatorPropertyPathPrefix = 'validatorConfiguration[MeasurementValidator]';
        /** @var array<array<array-key, string>> $validatorConfiguration */
        $validatorConfiguration = $data->validatorConfiguration;
        $selectedType = $validatorConfiguration['MeasurementValidator']['measurementType'] ?? reset($types);
        $selectedUnit = $validatorConfiguration['MeasurementValidator']['measurementUnit'] ?? null;

        $fieldDefinitionForm
            ->add(
                'measurementType',
                ChoiceType::class,
                [
                    'property_path' => $validatorPropertyPathPrefix . '[measurementType]',
                    'required' => true,
                    'label' => /** @Desc("Measurement Type") */ 'field_definition.ibexa_measurement.measurement_type',
                    'choices' => $types,
                    'disabled' => $isTranslation,
                    'data' => $selectedType,
                ]
            )
            ->add(
                'measurementUnit',
                ChoiceType::class,
                [
                    'property_path' => $validatorPropertyPathPrefix . '[measurementUnit]',
                    'required' => true,
                    'label' => /** @Desc("Base Unit") */ 'field_definition.ibexa_measurement.measurement_unit',
                    'choices' => $this->measurementTypes->getUnitsByTypes(),
                    'disabled' => $isTranslation,
                    'data' => $selectedUnit,
                ]
            )
            ->add(
                'measurementInputType',
                ChoiceType::class,
                [
                    'property_path' => $validatorPropertyPathPrefix . '[inputType]',
                    'required' => true,
                    'label' => /** @Desc("Input Type") */ 'field_definition.ibexa_measurement.measurement_input_type',
                    'choices' => [
                        /** @Desc("Simple Input") */
                        'field_definition.ibexa_measurement.measurement_input_type.value.simple' => MeasurementType::INPUT_TYPE_SIMPLE_INPUT,
                        /** @Desc("Range") */
                        'field_definition.ibexa_measurement.measurement_input_type.value.range' => MeasurementType::INPUT_TYPE_RANGE,
                    ],
                    'disabled' => $isTranslation,
                ]
            )
            ->add(
                'measurementSign',
                ChoiceType::class,
                [
                    'property_path' => $validatorPropertyPathPrefix . '[sign]',
                    'required' => true,
                    'label' => /** @Desc("Sign") */ 'field_definition.ibexa_measurement.measurement_sign',
                    'choices' => [
                        /** @Desc("none") */
                        'field_definition.ibexa_measurement.measurement_sign.value.none' => 'none',
                        /** @Desc("> greater than") */
                        'field_definition.ibexa_measurement.measurement_sign.value.gt' => 'gt',
                        /** @Desc("< less than") */
                        'field_definition.ibexa_measurement.measurement_sign.value.lt' => 'lt',
                        /** @Desc("≥ greater than or equal to") */
                        'field_definition.ibexa_measurement.measurement_sign.value.gte' => 'gte',
                        /** @Desc("≤ less than or equal to") */
                        'field_definition.ibexa_measurement.measurement_sign.value.lte' => 'lte',
                        /** @Desc("± plus/minus") */
                        'field_definition.ibexa_measurement.measurement_sign.value.pm' => 'pm',
                    ],
                    'disabled' => $isTranslation,
                ]
            )
            ->add(
                'minimum',
                NumberType::class,
                [
                    'property_path' => $validatorPropertyPathPrefix . '[minimum]',
                    'required' => false,
                    'label' => /** @Desc("Minimum Value") */ 'field_definition.ibexa_measurement.minimum',
                    'disabled' => $isTranslation,
                ]
            )
            ->add(
                'maximum',
                NumberType::class,
                [
                    'property_path' => $validatorPropertyPathPrefix . '[maximum]',
                    'required' => false,
                    'label' => /** @Desc("Maximum Value") */ 'field_definition.ibexa_measurement.maximum',
                    'disabled' => $isTranslation,
                ]
            )
            ->add(
                'defaultValue',
                NumberType::class,
                [
                    'property_path' => $validatorPropertyPathPrefix . '[defaultValue]',
                    'required' => false,
                    'label' => /** @Desc("Default Value") */ 'field_definition.ibexa_measurement.default_value',
                    'disabled' => $isTranslation,
                ]
            )
            ->add(
                'defaultRangeMinimumValue',
                NumberType::class,
                [
                    'property_path' => $validatorPropertyPathPrefix . '[defaultRangeMinimumValue]',
                    'required' => false,
                    'label' => /** @Desc("Default Range Minimum") */ 'field_definition.ibexa_measurement.default_range_minimum',
                    'disabled' => $isTranslation,
                ]
            )
            ->add(
                'defaultRangeMaximumValue',
                NumberType::class,
                [
                    'property_path' => $validatorPropertyPathPrefix . '[defaultRangeMaximumValue]',
                    'required' => false,
                    'label' => /** @Desc("Default Range Maximum") */ 'field_definition.ibexa_measurement.default_range_maximum',
                    'disabled' => $isTranslation,
                ]
            )
        ;
    }

    /**
     * Maps Field form to current FieldType.
     * Allows to add form fields for content edition.
     *
     * @param \Symfony\Component\Form\FormInterface $fieldForm form for the current Field
     * @param \Ibexa\Contracts\ContentForms\Data\Content\FieldData $data underlying data for current Field form
     */
    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data): void
    {
        $fieldDefinition = $data->fieldDefinition;
        $formConfig = $fieldForm->getConfig();
        $measurementValidator = $fieldDefinition->getValidatorConfiguration()['MeasurementValidator'];

        $options = [
            'label' => $fieldDefinition->getName(),
            'required' => $fieldDefinition->isRequired,
            'measurementType' => $measurementValidator['measurementType'],
            'measurementUnit' => $measurementValidator['measurementUnit'],
            'inputType' => $measurementValidator['inputType'],
            'minimum' => $measurementValidator['minimum'],
            'maximum' => $measurementValidator['maximum'],
        ];

        switch ($measurementValidator['inputType']) {
            case MeasurementType::INPUT_TYPE_SIMPLE_INPUT:
                $options = array_merge($options, [
                    'sign' => $measurementValidator['sign'] ?? null,
                    'defaultValue' => $measurementValidator['defaultValue'] ?? null,
                ]);

                $measurementInput = $formConfig->getFormFactory()->createBuilder()
                    ->create(
                        'value',
                        SingleMeasurementType::class,
                        $options,
                    )
                    ->setAutoInitialize(false)
                    ->getForm();

                break;
            case MeasurementType::INPUT_TYPE_RANGE:
                $options = array_merge($options, [
                    'defaultRangeMinimumValue' => $measurementValidator['defaultRangeMinimumValue'] ?? null,
                    'defaultRangeMaximumValue' => $measurementValidator['defaultRangeMaximumValue'] ?? null,
                ]);

                $measurementInput = $formConfig->getFormFactory()->createBuilder()
                    ->create(
                        'value',
                        RangeMeasurementType::class,
                        $options,
                    )
                    ->setAutoInitialize(false)
                    ->getForm();

                break;
        }

        if (isset($measurementInput)) {
            $fieldForm->add($measurementInput);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'translation_domain' => 'content_type',
            ]);
    }
}
