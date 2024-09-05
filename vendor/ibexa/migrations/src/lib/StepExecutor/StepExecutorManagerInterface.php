<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use Ibexa\Migration\ValueObject\Step\StepInterface;

interface StepExecutorManagerInterface
{
    public function handle(StepInterface $step): void;
}
