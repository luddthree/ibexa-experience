<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use Ibexa\Migration\ValueObject\Step\StepInterface;

interface StepExecutorInterface
{
    public function canHandle(StepInterface $step): bool;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     * @throws \Exception any exception occuring in step executors should be rethrown
     */
    public function handle(StepInterface $step): void;
}

class_alias(StepExecutorInterface::class, 'Ibexa\Platform\Migration\StepExecutor\StepExecutorInterface');
