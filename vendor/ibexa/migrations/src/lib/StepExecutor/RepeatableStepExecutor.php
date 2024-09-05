<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use Ibexa\Contracts\Migration\StepExecutor\AbstractStepExecutor;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\ValueObject\Step\RepeatableStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Psr\Log\LoggerAwareInterface;
use Webmozart\Assert\Assert;

final class RepeatableStepExecutor extends AbstractStepExecutor implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private StepExecutorManager $executorManager;

    public function __construct(StepExecutorManager $executorManager)
    {
        $this->executorManager = $executorManager;
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof RepeatableStep;
    }

    protected function doHandle(StepInterface $step)
    {
        Assert::isInstanceOf($step, RepeatableStep::class);

        foreach ($step->getSteps() as $innerStep) {
            $this->executorManager->handle($innerStep);
        }

        return null;
    }
}
