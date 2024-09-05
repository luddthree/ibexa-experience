<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\StepExecutor;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Segmentation\Value\SegmentGroupUpdateStruct;
use Ibexa\Segmentation\Value\Step\SegmentGroupUpdateStep;
use Psr\Log\LoggerAwareInterface;

final class SegmentGroupUpdateStepExecutor extends AbstractSegmentStepExecutor implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof SegmentGroupUpdateStep;
    }

    /**
     * @param \Ibexa\Segmentation\Value\Step\SegmentGroupUpdateStep $step
     */
    protected function doHandle(StepInterface $step): ValueObject
    {
        $segmentGroup = $this->findSegmentGroup($step->getMatcher());

        $updateStruct = new SegmentGroupUpdateStruct([
            'identifier' => $step->getIdentifier() ?? $segmentGroup->identifier,
            'name' => $step->getName() ?? $segmentGroup->name,
        ]);

        $this->segmentationService->updateSegmentGroup($segmentGroup, $updateStruct);

        $this->getLogger()->notice(sprintf(
            'Updated segment group named "%s" (ID: %s)',
            $segmentGroup->name,
            $segmentGroup->id
        ));

        return $segmentGroup;
    }
}
