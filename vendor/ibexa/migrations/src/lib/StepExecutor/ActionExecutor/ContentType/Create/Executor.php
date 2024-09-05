<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor\ActionExecutor\ContentType\Create;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\StepExecutor\ActionExecutor\AbstractExecutor;
use Ibexa\Migration\ValueObject\Step\Action;
use Webmozart\Assert\Assert;

final class Executor extends AbstractExecutor
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $valueObject
     */
    protected function prepareLogMessage(Action $action, ValueObject $valueObject): ?string
    {
        Assert::isInstanceOf($valueObject, ContentType::class);

        return sprintf('[Step][Action] Executing create content_type action %s', $action->getSupportedType());
    }
}

class_alias(Executor::class, 'Ibexa\Platform\Migration\StepExecutor\ActionExecutor\ContentType\Create\Executor');
