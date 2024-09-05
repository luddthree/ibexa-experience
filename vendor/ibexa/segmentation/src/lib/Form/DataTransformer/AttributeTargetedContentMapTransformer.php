<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Form\DataTransformer;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class AttributeTargetedContentMapTransformer implements DataTransformerInterface
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

    public function transform($value): ?string
    {
        if (null === $value) {
            return json_encode([]);
        }

        $data = json_decode($value, true);

        $entries = [];
        foreach ($data as $entryData) {
            if (
                !is_array($entryData)
                || !array_key_exists('segmentId', $entryData)
                || !array_key_exists('locationId', $entryData)
            ) {
                throw new TransformationFailedException("Keys 'segmentId' or 'locationId' don't exist in input data.");
            }

            try {
                $location = $this->locationService->loadLocation($entryData['locationId']);
                $segment = $this->segmentationService->loadSegment($entryData['segmentId']);
            } catch (NotFoundException $e) {
                continue;
            }

            $entries[] = [
                'segment' => [
                    'id' => $segment->id,
                    'name' => $segment->name,
                ],
                'content' => [
                    'locationId' => $location->id,
                    'name' => $location->getContentInfo()->name,
                    'breadcrumbs' => $this->createBreadcrumbsString($location),
                ],
            ];
        }

        return json_encode($entries);
    }

    public function reverseTransform($value): ?string
    {
        if (empty($value)) {
            return json_encode([]);
        }

        $data = json_decode($value, true);

        $entries = [];
        foreach ($data as $entryData) {
            if (
                !is_array($entryData)
                || !isset($entryData['segment']['id'])
                || !isset($entryData['content']['locationId'])
            ) {
                throw new TransformationFailedException("Can't find Segment and Location data.");
            }

            $entries[] = [
                'segmentId' => $entryData['segment']['id'],
                'locationId' => $entryData['content']['locationId'],
            ];
        }

        return json_encode($entries);
    }

    private function createBreadcrumbsString(Location $location): string
    {
        $path = $location->path;
        array_shift($path); // removes Top Level Node
        array_pop($path); // removes $location from path

        $locations = array_map([$this->locationService, 'loadLocation'], $path);

        $parentLocationNames = [];
        foreach ($locations as $parentLocation) {
            $parentLocationNames[] = $parentLocation->getContentInfo()->name;
        }

        return implode(' / ', $parentLocationNames);
    }
}

class_alias(AttributeTargetedContentMapTransformer::class, 'Ibexa\Platform\Segmentation\Form\DataTransformer\AttributeTargetedContentMapTransformer');
