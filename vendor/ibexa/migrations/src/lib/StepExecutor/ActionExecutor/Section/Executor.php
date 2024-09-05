<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor\ActionExecutor\Section;

use Ibexa\Contracts\Core\Repository\Values\Content\Section;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\StepExecutor\ActionExecutor\AbstractExecutor;
use Ibexa\Migration\ValueObject\Step\Action;
use Webmozart\Assert\Assert;

final class Executor extends AbstractExecutor
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject|\Ibexa\Contracts\Core\Repository\Values\Content\Section $valueObject
     */
    protected function prepareLogMessage(Action $action, ValueObject $valueObject): ?string
    {
        Assert::isInstanceOf($valueObject, Section::class);

        return sprintf('[Step][Action] Executing section action %s', $action->getSupportedType());
    }
}
