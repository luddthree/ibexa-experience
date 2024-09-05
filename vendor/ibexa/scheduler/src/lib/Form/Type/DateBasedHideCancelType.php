<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Form\Type;

use Ibexa\AdminUi\Form\Type\Content\LocationType;
use Ibexa\AdminUi\Form\Type\Content\VersionInfoType;
use Ibexa\Scheduler\Form\Data\DateBasedHideCancelData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateBasedHideCancelType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('location', LocationType::class);
        $builder->add('versionInfo', VersionInfoType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DateBasedHideCancelData::class,
        ]);
    }
}

class_alias(DateBasedHideCancelType::class, 'EzSystems\DateBasedPublisher\Core\Form\Type\DateBasedHideCancelType');
