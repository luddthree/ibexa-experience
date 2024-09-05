<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\StepExecutor;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentList;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Filter\Filter;
use Ibexa\Migration\StepExecutor\UserGroupDeleteStepExecutor;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Migration\ValueObject\Step\UserGroupDeleteStep;
use Ibexa\Migration\ValueObject\UserGroup\Matcher;

/**
 * @covers \Ibexa\Migration\StepExecutor\UserGroupDeleteStepExecutor
 */
final class UserGroupDeleteStepExecutorTest extends AbstractInitializedStepExecutorTest
{
    private const KNOWN_USER_GROUP_ID = 13;
    private const KNOWN_USER_GROUP_REMOTE_ID = '3c160cca19fb135f83bd02d911f04db2';

    /**
     * @dataProvider provideSteps
     */
    public function testHandle(StepInterface $step): void
    {
        self::assertSame(6, $this->findUserGroups()->getTotalCount());

        $content = self::getContentService()->loadContentByRemoteId(self::KNOWN_USER_GROUP_REMOTE_ID);
        self::assertNotNull($content);

        $executor = new UserGroupDeleteStepExecutor(
            self::getTransactionHandler(),
            self::getContentService(),
            self::getUserService(),
        );
        $executor->handle($step);

        self::assertSame(5, $this->findUserGroups()->getTotalCount());

        self::expectException(NotFoundException::class);
        self::getContentService()->loadContentByRemoteId(self::KNOWN_USER_GROUP_REMOTE_ID);
    }

    private function findUserGroups(): ContentList
    {
        $filter = new Filter();
        $filter->withCriterion(new LogicalAnd([
            new ContentTypeIdentifier('user_group'),
        ]));

        return self::getContentService()->find($filter);
    }

    /**
     * @return iterable<array<UserGroupDeleteStep>>
     */
    public function provideSteps(): iterable
    {
        yield [
            new UserGroupDeleteStep(
                new Matcher('remoteId', self::KNOWN_USER_GROUP_REMOTE_ID)
            ),
        ];

        yield [
            new UserGroupDeleteStep(
                new Matcher('id', self::KNOWN_USER_GROUP_ID)
            ),
        ];
    }
}

class_alias(UserGroupDeleteStepExecutorTest::class, 'Ibexa\Platform\Tests\Migration\StepExecutor\UserGroupDeleteStepExecutorTest');
