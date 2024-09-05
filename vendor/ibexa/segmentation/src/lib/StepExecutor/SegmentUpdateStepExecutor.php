<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\StepExecutor;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface;
use Ibexa\Migration\ValueObject\Step\ActionsAwareStepInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Segmentation\Value\Segment;
use Ibexa\Segmentation\Value\SegmentMatcher;
use Ibexa\Segmentation\Value\SegmentUpdateStruct;
use Ibexa\Segmentation\Value\Step\SegmentUpdateStep;
use Psr\Log\LoggerAwareInterface;
use Webmozart\Assert\Assert;

final class SegmentUpdateStepExecutor extends AbstractSegmentStepExecutor implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var \Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface */
    private $actionExecutor;

    public function __construct(
        SegmentationServiceInterface $segmentationService,
        ExecutorInterface $actionExecutor
    ) {
        parent::__construct($segmentationService);
        $this->actionExecutor = $actionExecutor;
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof SegmentUpdateStep;
    }

    /**
     * @param \Ibexa\Segmentation\Value\Step\SegmentUpdateStep $step
     */
    protected function doHandle(StepInterface $step): ValueObject
    {
        Assert::isInstanceOf($step->getMatcher(), SegmentMatcher::class);

        $segment = $this->findSegment($step->getMatcher());

        $updateStruct = new SegmentUpdateStruct([
            'identifier' => $step->getIdentifier() ?? $segment->identifier,
            'name' => $step->getName() ?? $segment->name,
            'group' => $segment->group,
        ]);

        $segment = $this->segmentationService->updateSegment($segment, $updateStruct);

        $this->getLogger()->notice(sprintf(
            'Updated segment %s (ID: %s)',
            $segment->group->name,
            $segment->group->id
        ));

        return $segment;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject $executionResult
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function handleActions(ActionsAwareStepInterface $step, $executionResult): void
    {
        Assert::isInstanceOf($executionResult, Segment::class);

        foreach ($step->getActions() as $action) {
            $this->actionExecutor->handle($action, $executionResult);
        }
    }
}
