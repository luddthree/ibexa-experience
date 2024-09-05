<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor\ActionExecutor;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\ValueObject\Step\Action;

interface ExecutorInterface
{
    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     */
    public function handle(Action $action, ValueObject $valueObject): void;
}

class_alias(ExecutorInterface::class, 'Ibexa\Platform\Migration\StepExecutor\ActionExecutor\ExecutorInterface');
