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
use Ibexa\Migration\StepExecutor\ActionExecutor\UserGroup\Executor;
use Ibexa\Migration\StepExecutor\ReferenceDefinition\ResolverInterface;
use Ibexa\Migration\StepExecutor\UserGroupCreateStepExecutor;
use Ibexa\Migration\ValueObject\Content\Field;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Migration\ValueObject\Step\UserGroupCreateStep;
use Ibexa\Migration\ValueObject\UserGroup\CreateMetadata;

/**
 * @covers \Ibexa\Migration\StepExecutor\UserGroupCreateStepExecutor
 */
final class UserGroupCreateStepExecutorTest extends AbstractInitializedStepExecutorTest
{
    /**
     * @dataProvider provideStep
     */
    public function testHandle(StepInterface $step): void
    {
        self::assertSame(6, $this->findUserGroups()->getTotalCount());
        $found = true;
        try {
            self::getContentService()->loadContentByRemoteId('__foo__');
        } catch (NotFoundException $e) {
            $found = false;
        }
        self::assertFalse($found);

        $executor = new UserGroupCreateStepExecutor(
            self::getUserService(),
            self::getContentTypeService(),
            self::getFieldTypeService(),
            self::getRoleService(),
            self::getServiceByClassName(Executor::class),
        );
        $this->configureExecutor($executor, [
            ResolverInterface::class => self::getReferenceResolver('user_group'),
        ]);

        $executor->handle($step);

        self::assertSame(7, $this->findUserGroups()->getTotalCount());
        $content = self::getContentService()->loadContentByRemoteId('__foo__');
        self::assertSame('__user_group_name__', (string) $content->getFieldValue('name'));

        $userGroup = self::getUserService()->loadUserGroup($content->id);
        $roleAssignments = self::getRoleService()->getRoleAssignmentsForUserGroup($userGroup);

        /** @var string[] $roleIdentifiers */
        $roleIdentifiers = [];
        foreach ($roleAssignments as $roleAssignment) {
            $roleIdentifiers[] = $roleAssignment->getRole()->identifier;
        }

        self::assertEquals(['Anonymous'], $roleIdentifiers);
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
     * @return iterable<string, array<\Ibexa\Migration\ValueObject\Step\UserGroupCreateStep>>
     */
    public function provideStep(): iterable
    {
        $fields = [
            Field::createFromArray([
                'fieldDefIdentifier' => 'name',
                'languageCode' => 'eng-GB',
                'value' => '__user_group_name__',
            ]),
        ];

        yield 'step with role identifier' => [
            new UserGroupCreateStep(
                CreateMetadata::createFromArray([
                    'remoteId' => '__foo__',
                    'parentGroupId' => 13,
                    'mainLanguage' => 'eng-GB',
                ]),
                $fields,
                [
                    'Anonymous',
                ]
            ),
        ];

        yield 'step with role id' => [
            new UserGroupCreateStep(
                CreateMetadata::createFromArray([
                    'remoteId' => '__foo__',
                    'parentGroupId' => 13,
                    'mainLanguage' => 'eng-GB',
                ]),
                $fields,
                [
                    '1',
                ]
            ),
        ];
    }
}

class_alias(UserGroupCreateStepExecutorTest::class, 'Ibexa\Platform\Tests\Migration\StepExecutor\UserGroupCreateStepExecutorTest');
