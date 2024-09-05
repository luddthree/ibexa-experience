<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type\Model;

use Ibexa\Personalization\Value\Model\SegmentItemGroup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SegmentGroupType extends AbstractType
{
    public function getBlockPrefix(): string
    {
        return 'personalization_segment_segment_group';
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('groupId', HiddenType::class);

        $builder->add(
            'segmentsData',
            CollectionType::class,
            [
                'entry_type' => SegmentGroupEntryType::class,
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => [
                    'label' => false,
                ],
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined(['groupId', 'segmentsData']);

        $resolver->setDefaults([
            'data_class' => SegmentItemGroup::class,
        ]);
    }
}
