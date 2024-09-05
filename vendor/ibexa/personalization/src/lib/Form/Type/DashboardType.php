<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DashboardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('chart', DateTimeRangeType::class, [
                'label' => false,
            ])
            ->add('popularity', PopularityDurationType::class, [
                'label' => false,
            ]);

        if ($builder->getOption('is_commerce')) {
            $builder->add('revenue', DateTimeRangeType::class, [
                'label' => false,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'is_commerce' => null,
        ])
        ->setAllowedTypes('is_commerce', 'bool');
    }
}

class_alias(DashboardType::class, 'Ibexa\Platform\Personalization\Form\Type\DashboardType');
