<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\StepExecutor;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentList;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Filter\Filter;
use Ibexa\Migration\StepExecutor\ActionExecutor;

abstract class AbstractContentStepExecutorTest extends AbstractInitializedStepExecutorTest
{
    protected const KNOWN_CONTENT_TYPE_IDENTIFIER = 'landing_page';

    final protected static function getContentActionExecutor(): ActionExecutor\ExecutorInterface
    {
        return self::getServiceByClassName(ActionExecutor\Content\Create\Executor::class);
    }

    final protected static function findContentByIdentifier(string $contentTypeIdentifier): ContentList
    {
        $filter = new Filter();
        $filter->andWithCriterion(new LogicalAnd([
            new ContentTypeIdentifier($contentTypeIdentifier),
        ]));

        return self::getContentService()->find($filter);
    }
}

class_alias(AbstractContentStepExecutorTest::class, 'Ibexa\Platform\Tests\Migration\StepExecutor\AbstractContentStepExecutorTest');
