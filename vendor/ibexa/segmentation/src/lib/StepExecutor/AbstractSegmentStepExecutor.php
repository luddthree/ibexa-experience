<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\StepExecutor;

use Ibexa\Contracts\Migration\StepExecutor\AbstractStepExecutor;
use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Ibexa\Segmentation\Value\Segment;
use Ibexa\Segmentation\Value\SegmentGroup;
use Ibexa\Segmentation\Value\SegmentGroupMatcher;
use Ibexa\Segmentation\Value\SegmentMatcher;
use InvalidArgumentException;

abstract class AbstractSegmentStepExecutor extends AbstractStepExecutor
{
    /** @var \Ibexa\Contracts\Segmentation\SegmentationServiceInterface */
    protected $segmentationService;

    public function __construct(
        SegmentationServiceInterface $segmentationService
    ) {
        $this->segmentationService = $segmentationService;
    }

    final protected function findSegmentGroup(SegmentGroupMatcher $matcher): SegmentGroup
    {
        if ($matcher->getId() !== null) {
            return $this->segmentationService->loadSegmentGroup($matcher->getId());
        }

        if ($matcher->getIdentifier() !== null) {
            foreach ($this->segmentationService->loadSegmentGroups() as $segmentGroup) {
                if ($segmentGroup->identifier === $matcher->getIdentifier()) {
                    return $segmentGroup;
                }
            }
            throw new InvalidArgumentException(sprintf(
                'Unable to find Segment Group with identifier: "%s"',
                $matcher->getIdentifier(),
            ));
        }

        throw new InvalidArgumentException('Invalid group matcher missing id or identifier');
    }

    final protected function findSegment(SegmentMatcher $matcher): Segment
    {
        if ($matcher->getId() !== null) {
            return $this->segmentationService->loadSegment($matcher->getId());
        }

        if ($matcher->getIdentifier() !== null) {
            return $this->segmentationService->loadSegmentByIdentifier($matcher->getIdentifier());
        }

        throw new InvalidArgumentException('Invalid matcher missing id or identifier');
    }
}
