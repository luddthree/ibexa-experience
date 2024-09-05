<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\StepExecutor;

use Ibexa\Migration\StepExecutor\ActionExecutor;
use Ibexa\Migration\StepExecutor\ContentType\ContentTypeFinderRegistryInterface;

abstract class AbstractContentTypeExecutorTest extends AbstractInitializedStepExecutorTest
{
    final protected static function getContentTypeActionExecutor(): ActionExecutor\ExecutorInterface
    {
        return self::getServiceByClassName(ActionExecutor\ContentType\Update\Executor::class);
    }

    final protected static function getContentTypeFinderRegistry(): ContentTypeFinderRegistryInterface
    {
        return self::getServiceByClassName(ContentTypeFinderRegistryInterface::class);
    }
}
