<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\Form\Type\FieldType;

use Ibexa\ContentForms\FieldType\DataTransformer\FieldValueTransformer;
use Ibexa\Contracts\Core\Repository\FieldTypeService;
use Ibexa\Measurement\FieldType\MeasurementType as MeasurementFieldType;
use Ibexa\Measurement\Form\Type\MeasurementType as BaseMeasurementType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class MeasurementType extends AbstractType
{
    protected FieldTypeService $fieldTypeService;

    public function __construct(
        FieldTypeService $fieldTypeService
    ) {
        $this->fieldTypeService = $fieldTypeService;
    }

    public function getParent(): string
    {
        return BaseMeasurementType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'inputType',
                HiddenType::class,
                [
                    'data' => $options['inputType'],
                    'attr' => [
                        'readonly' => true,
                    ],
                ]
            );

        $builder
            ->addModelTransformer(
                new FieldValueTransformer($this->fieldTypeService->getFieldType('ibexa_measurement'))
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['inputType']);
        $resolver->setAllowedTypes('inputType', 'integer');
        $resolver->setAllowedValues('inputType', [
            MeasurementFieldType::INPUT_TYPE_RANGE,
            MeasurementFieldType::INPUT_TYPE_SIMPLE_INPUT,
        ]);
    }
}
