<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Dashboard\Form\Type;

use Ibexa\Bundle\Dashboard\Form\Data\DashboardChangeActiveData;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DashboardChangeActiveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'location',
                AvailableDashboardType::class,
                [
                    'label' => false,
                    'current_dashboard' => $options['current_dashboard'],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DashboardChangeActiveData::class,
            'translation_domain' => 'forms',
        ]);
        $resolver->setRequired('current_dashboard');
        $resolver->setAllowedTypes('current_dashboard', [Location::class]);
    }
}
