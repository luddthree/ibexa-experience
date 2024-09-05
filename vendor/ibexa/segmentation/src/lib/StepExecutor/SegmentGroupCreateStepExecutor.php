<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\StepExecutor;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\Migration\StepExecutor\AbstractStepExecutor;
use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Segmentation\Value\SegmentGroupCreateStruct;
use Ibexa\Segmentation\Value\Step\SegmentGroupCreateStep;
use Psr\Log\LoggerAwareInterface;

final class SegmentGroupCreateStepExecutor extends AbstractStepExecutor implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var \Ibexa\Contracts\Segmentation\SegmentationServiceInterface */
    private $segmentationService;

    public function __construct(
        SegmentationServiceInterface $segmentationService
    ) {
        $this->segmentationService = $segmentationService;
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof SegmentGroupCreateStep;
    }

    /**
     * @param \Ibexa\Segmentation\Value\Step\SegmentGroupCreateStep $step
     */
    protected function doHandle(StepInterface $step): ValueObject
    {
        $createStruct = new SegmentGroupCreateStruct([
            'identifier' => $step->getIdentifier(),
            'name' => $step->getName(),
            'createSegments' => [],
        ]);

        $segmentGroup = $this->segmentationService->createSegmentGroup($createStruct);

        $this->getLogger()->notice(sprintf(
            'Added segment group "%s" (ID: %s)',
            $segmentGroup->name,
            $segmentGroup->id
        ));

        return $segmentGroup;
    }
}
