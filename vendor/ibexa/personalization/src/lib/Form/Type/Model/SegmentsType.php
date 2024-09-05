<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type\Model;

use Ibexa\Personalization\Form\Data\SegmentsData;
use Ibexa\Personalization\Value\Model\SegmentList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SegmentsType extends AbstractType
{
    public function getBlockPrefix(): string
    {
        return 'personalization_segments';
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var \Ibexa\Personalization\Form\Data\SegmentsData $segments */
        $segments = $options['segments'];

        $builder->add(
            'segmentItemGroups',
            SegmentGroupsType::class,
        );

        $builder->add(
            'activeSegments',
            CollectionType::class,
            [
                'entry_type' => SegmentType::class,
                'prototype' => false,
                'allow_add' => false,
                'entry_options' => [
                    'label' => false,
                ],
                'disabled' => true,
                'data' => $segments->getActiveSegments(),
            ]
        );

        $builder->add(
            'inactiveSegments',
            CollectionType::class,
            [
                'entry_type' => SegmentType::class,
                'prototype' => false,
                'allow_add' => false,
                'entry_options' => [
                    'label' => false,
                ],
                'disabled' => true,
                'data' => $segments->getInactiveSegments(),
            ]
        );
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        /** @var \Ibexa\Personalization\Form\Data\SegmentsData $data */
        $data = $options['segments'];

        $segmentList = array_merge(
            $data->getActiveSegments()->getSegments(),
            $data->getInactiveSegments()->getSegments(),
        );

        $groups = [];
        foreach ($segmentList as $segment) {
            $group = $segment->getGroup();
            $groups[$group->getId()] = $group;
        }

        $view->vars['segment_groups'] = $groups;
        $view->vars['active_segments_group_map'] = $this->createSegmentsGroupMap($data->getActiveSegments());
        $view->vars['inactive_segments_group_map'] = $this->createSegmentsGroupMap($data->getInactiveSegments());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined(['segments']);

        $resolver->setDefaults([
            'data_class' => SegmentsData::class,
        ]);
    }

    /**
     * @return array<int, array<\Ibexa\Personalization\Value\Model\Segment>>
     */
    private function createSegmentsGroupMap(SegmentList $segmentList): array
    {
        $segmentGroupMap = [];

        foreach ($segmentList->getSegments() as $segment) {
            $groupId = $segment->getGroup()->getId();

            if (!array_key_exists($groupId, $segmentGroupMap)) {
                $activeSegmentsGroupMap[$groupId] = [];
            }

            $segmentGroupMap[$groupId][] = $segment;
        }

        return $segmentGroupMap;
    }
}
