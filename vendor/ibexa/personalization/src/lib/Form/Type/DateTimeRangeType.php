<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type;

use Ibexa\Personalization\Form\Data\DateTimeRangeData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DateTimeRangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('period', DateIntervalType::class, [
                'label' => false,
            ])
            ->add('select', TimePeriodChoiceType::class, [
                'mapped' => false,
                'label' => false,
                'custom_range' => $builder->getOption('custom_range'),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DateTimeRangeData::class,
            'custom_range' => true,
        ]);
        $resolver->setAllowedTypes('custom_range', 'bool');
    }
}

class_alias(DateTimeRangeType::class, 'Ibexa\Platform\Personalization\Form\Type\DateTimeRangeType');
