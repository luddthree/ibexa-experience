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
use Ibexa\Segmentation\Value\SegmentMatcher;
use Ibexa\Segmentation\Value\Step\SegmentDeleteStep;
use Psr\Log\LoggerAwareInterface;
use Webmozart\Assert\Assert;

final class SegmentDeleteStepExecutor extends AbstractSegmentStepExecutor implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof SegmentDeleteStep;
    }

    /**
     * @param \Ibexa\Segmentation\Value\Step\SegmentDeleteStep $step
     */
    protected function doHandle(StepInterface $step): ?ValueObject
    {
        Assert::isInstanceOf($step->getMatcher(), SegmentMatcher::class);

        $segment = $this->findSegment($step->getMatcher());

        $this->segmentationService->removeSegment($segment);

        $this->getLogger()->notice(sprintf(
            'Added segment [%s] "%s" (ID: %s)',
            $segment->group->name,
            $segment->name,
            $segment->id
        ));

        return null;
    }
}
