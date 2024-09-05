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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SingleAttributeMeasurementType extends AbstractType
{
    private MeasurementServiceInterface $measurementService;

    public function __construct(MeasurementServiceInterface $measurementService)
    {
        $this->measurementService = $measurementService;
    }

    public function getBlockPrefix(): string
    {
        return 'ibexa_fieldtype_measurement_single';
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
                'value',
                NumberType::class,
                [
                    'label' => /** @Desc("Value") */ 'content.field_type.ibexa_measurement.value',
                    'required' => $options['required'],
                    'attr' => $valueAttr,
                ]
            );

        $builder->addModelTransformer(new SingleAttributeModelTransformer($this->measurementService));
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
            'translation_domain' => 'ibexa_measurement_fieldtype',
            'sign' => null,
            'minimum' => null,
            'maximum' => null,
            'defaultValue' => null,
        ]);

        $resolver->setAllowedTypes('sign', ['null', 'string']);
        $resolver->setAllowedTypes('minimum', ['null', 'float']);
        $resolver->setAllowedTypes('maximum', ['null', 'float']);
        $resolver->setAllowedTypes('defaultValue', ['null', 'float']);
    }
}
