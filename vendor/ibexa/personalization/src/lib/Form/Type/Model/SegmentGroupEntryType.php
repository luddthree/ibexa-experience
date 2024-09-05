<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type\Model;

use Ibexa\Personalization\Value\Model\SegmentData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SegmentGroupEntryType extends AbstractType
{
    public function getBlockPrefix(): string
    {
        return 'personalization_segment_segment_group_entry';
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('segment', HiddenType::class);
        $builder->add('group', HiddenType::class);
        $builder->add('active', HiddenType::class, ['mapped' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SegmentData::class,
        ]);
    }
}
