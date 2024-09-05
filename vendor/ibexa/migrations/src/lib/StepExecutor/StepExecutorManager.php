<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use Ibexa\Migration\ValueObject\Step\StepInterface;
use InvalidArgumentException;

final class StepExecutorManager implements StepExecutorManagerInterface
{
    /** @var iterable<StepExecutorInterface> */
    private $executors;

    /**
     * @param iterable<StepExecutorInterface> $executors
     */
    public function __construct(iterable $executors = [])
    {
        $this->executors = $executors;
    }

    public function handle(StepInterface $step): void
    {
        foreach ($this->executors as $executor) {
            if ($executor->canHandle($step)) {
                $executor->handle($step);

                return;
            }
        }

        throw new InvalidArgumentException(
            sprintf(
                'Unable to execute step %s. Make sure step executor service for it is properly registered.',
                get_class($step)
            )
        );
    }
}

class_alias(StepExecutorManager::class, 'Ibexa\Platform\Migration\StepExecutor\StepExecutorManager');
