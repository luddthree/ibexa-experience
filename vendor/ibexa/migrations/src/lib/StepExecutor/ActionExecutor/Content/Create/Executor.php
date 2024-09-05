<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor\ActionExecutor\Content\Create;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\StepExecutor\ActionExecutor\AbstractExecutor;
use Ibexa\Migration\ValueObject\Step\Action;
use Webmozart\Assert\Assert;

final class Executor extends AbstractExecutor
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $valueObject
     */
    protected function prepareLogMessage(Action $action, ValueObject $valueObject): ?string
    {
        Assert::isInstanceOf($valueObject, Content::class);

        return sprintf('[Step][Action] Executing create content action %s', $action->getSupportedType());
    }
}

class_alias(Executor::class, 'Ibexa\Platform\Migration\StepExecutor\ActionExecutor\Content\Create\Executor');
