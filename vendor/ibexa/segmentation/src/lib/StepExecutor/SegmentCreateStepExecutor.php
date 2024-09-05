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
use Ibexa\Segmentation\Value\SegmentCreateStruct;
use Ibexa\Segmentation\Value\Step\SegmentCreateStep;
use Psr\Log\LoggerAwareInterface;
use Webmozart\Assert\Assert;

final class SegmentCreateStepExecutor extends AbstractSegmentStepExecutor implements LoggerAwareInterface
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
        return $step instanceof SegmentCreateStep;
    }

    /**
     * @param \Ibexa\Segmentation\Value\Step\SegmentCreateStep $step
     */
    protected function doHandle(StepInterface $step): ValueObject
    {
        $segmentGroup = $this->findSegmentGroup($step->getGroup());

        $createStruct = new SegmentCreateStruct([
            'identifier' => $step->getIdentifier(),
            'name' => $step->getName(),
            'group' => $segmentGroup,
        ]);

        $segment = $this->segmentationService->createSegment($createStruct);

        $this->getLogger()->notice(sprintf(
            'Added segment [%s] "%s" (ID: %s)',
            $segmentGroup->name,
            $segment->name,
            $segment->id
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
