<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Form\Type;

use Ibexa\AdminUi\Form\Type\Content\LocationType;
use Ibexa\AdminUi\Form\Type\Content\VersionInfoType;
use Ibexa\Scheduler\Form\Data\DateBasedHideData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Positive;

final class DateBasedHideType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('location', LocationType::class);
        $builder->add('versionInfo', VersionInfoType::class);
        $builder->add('timestamp', HiddenType::class, [
            'constraints' => [
                new Positive(),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DateBasedHideData::class,
        ]);
    }
}

class_alias(DateBasedHideType::class, 'EzSystems\DateBasedPublisher\Core\Form\Type\DateBasedHideType');
