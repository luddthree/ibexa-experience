<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Component;

use Ibexa\Contracts\AdminUi\Component\Renderable;
use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Twig\Environment;

class InfobarComponent implements Renderable
{
    /** @var \Twig\Environment */
    private $twig;

    /** @var \Ibexa\Contracts\Segmentation\SegmentationServiceInterface */
    private $segmentationService;

    public function __construct(
        Environment $twig,
        SegmentationServiceInterface $segmentationService
    ) {
        $this->twig = $twig;
        $this->segmentationService = $segmentationService;
    }

    public function render(array $contextParameters = []): string
    {
        $parameters = ['segments' => []];

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

            $parameters['segments'][] = $groupData;
        }

        return $this->twig->render(
            '@ibexadesign/segmentation/page_builder/infobar/segmentation_list.html.twig',
            array_merge($contextParameters, $parameters)
        );
    }
}

class_alias(InfobarComponent::class, 'Ibexa\Platform\Segmentation\Component\InfobarComponent');
