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
use Ibexa\Segmentation\Value\Step\SegmentGroupDeleteStep;
use Psr\Log\LoggerAwareInterface;

final class SegmentGroupDeleteStepExecutor extends AbstractSegmentStepExecutor implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof SegmentGroupDeleteStep;
    }

    /**
     * @param \Ibexa\Segmentation\Value\Step\SegmentGroupDeleteStep $step
     */
    protected function doHandle(StepInterface $step): ?ValueObject
    {
        $segmentGroup = $this->findSegmentGroup($step->getMatcher());

        $this->segmentationService->removeSegmentGroup($segmentGroup);

        $this->getLogger()->notice(sprintf(
            'Remove segment group %s (ID: %s)',
            $segmentGroup->name,
            $segmentGroup->id
        ));

        return null;
    }
}
