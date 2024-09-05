<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\StepExecutor;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\RemoteId;
use Ibexa\Migration\StepExecutor\ContentDeleteStepExecutor;
use Ibexa\Migration\ValueObject\Step\ContentDeleteStep;

/**
 * @covers \Ibexa\Migration\StepExecutor\ContentDeleteStepExecutor
 */
final class ContentDeleteStepExecutorTest extends AbstractContentStepExecutorTest
{
    private const KNOWN_CONTENT_REMOTE_ID = '8a9c9c761004866fb458d89910f52bee';

    public function testHandle(): void
    {
        self::assertSame(1, self::findContentByIdentifier(self::KNOWN_CONTENT_TYPE_IDENTIFIER)->getTotalCount());
        self::getContentService()->loadContentByRemoteId(self::KNOWN_CONTENT_REMOTE_ID);

        $step = $this->createStep();

        $executor = new ContentDeleteStepExecutor(
            self::getContentService(),
            self::getContentActionExecutor(),
        );

        $this->configureExecutor($executor);
        $executor->handle($step);

        self::assertSame(0, self::findContentByIdentifier(self::KNOWN_CONTENT_TYPE_IDENTIFIER)->getTotalCount());
        $this->expectException(NotFoundException::class);
        self::getContentService()->loadContentByRemoteId(self::KNOWN_CONTENT_REMOTE_ID);
    }

    private function createStep(): ContentDeleteStep
    {
        return new ContentDeleteStep(
            new RemoteId(self::KNOWN_CONTENT_REMOTE_ID)
        );
    }
}

class_alias(ContentDeleteStepExecutorTest::class, 'Ibexa\Platform\Tests\Migration\StepExecutor\ContentDeleteStepExecutorTest');
