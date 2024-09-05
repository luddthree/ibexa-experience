<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Form\Type;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Ibexa\Segmentation\Form\DataTransformer\AttributeTargetedContentMapTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

final class AttributeTargetedContentMapType extends AbstractType
{
    /** @var \Ibexa\Contracts\Segmentation\SegmentationServiceInterface */
    private $segmentationService;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    public function __construct(
        SegmentationServiceInterface $segmentationService,
        LocationService $locationService
    ) {
        $this->segmentationService = $segmentationService;
        $this->locationService = $locationService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(
            new AttributeTargetedContentMapTransformer($this->segmentationService, $this->locationService)
        );
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $segmentsMap = [];
        foreach ($this->segmentationService->loadSegmentGroups() as $group) {
            $groupData = [
                'id' => $group->id,
                'name' => $group->name,
                'segments' => [],
            ];

            foreach ($this->segmentationService->loadSegmentsAssignedToGroup($group) as $segment) {
                $groupData['segments'][] = [
                    'id' => $segment->id,
                    'name' => $segment->name,
                ];
            }

            $segmentsMap[] = $groupData;
        }

        $view->vars['attr']['data-segments'] = json_encode($segmentsMap);
    }

    public function getParent(): ?string
    {
        return TextType::class;
    }

    public function getBlockPrefix(): ?string
    {
        return 'block_configuration_attribute_targeted_content_map';
    }
}

class_alias(AttributeTargetedContentMapType::class, 'Ibexa\Platform\Segmentation\Form\Type\AttributeTargetedContentMapType');
