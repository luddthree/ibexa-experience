<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\StepExecutor;

use Ibexa\Migration\StepExecutor\ActionExecutor\UserGroup\Executor;
use Ibexa\Migration\StepExecutor\ReferenceDefinition\ResolverInterface;
use Ibexa\Migration\StepExecutor\UserGroupUpdateStepExecutor;
use Ibexa\Migration\ValueObject\Content\Field;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Migration\ValueObject\Step\UserGroupUpdateStep;
use Ibexa\Migration\ValueObject\UserGroup\Matcher;
use Ibexa\Migration\ValueObject\UserGroup\UpdateMetadata;

/**
 * @covers \Ibexa\Migration\StepExecutor\UserGroupUpdateStepExecutor
 */
final class UserGroupUpdateStepExecutorTest extends AbstractInitializedStepExecutorTest
{
    private const KNOWN_USER_GROUP_ID = 13;
    private const KNOWN_USER_GROUP_REMOTE_ID = '3c160cca19fb135f83bd02d911f04db2';

    /**
     * @dataProvider provideStep
     */
    public function testHandle(StepInterface $step): void
    {
        $content = self::getContentService()->loadContentByRemoteId(self::KNOWN_USER_GROUP_REMOTE_ID);
        self::assertNotNull($content);

        $executor = new UserGroupUpdateStepExecutor(
            self::getUserService(),
            self::getContentTypeService(),
            self::getFieldTypeService(),
            self::getRoleService(),
            self::getServiceByClassName(Executor::class),
            self::getContentService()
        );

        $this->configureExecutor($executor, [
            ResolverInterface::class => self::getReferenceResolver('user_group'),
        ]);

        $executor->handle($step);

        $content = self::getContentService()->loadContentByRemoteId(self::KNOWN_USER_GROUP_REMOTE_ID);
        /** @var \Ibexa\Migration\ValueObject\Step\UserGroupUpdateStep $step */
        self::assertSame($step->fields[0]->value, (string) $content->getFieldValue('name'));

        $userGroup = self::getUserService()->loadUserGroup($content->id);
        $roleAssignments = self::getRoleService()->getRoleAssignmentsForUserGroup($userGroup);

        /** @var string[] $roleIdentifiers */
        $roleIdentifiers = [];
        foreach ($roleAssignments as $roleAssignment) {
            $roleIdentifiers[] = $roleAssignment->getRole()->identifier;
        }

        self::assertContains('Anonymous', $roleIdentifiers);
    }

    /**
     * @return iterable<string, array<\Ibexa\Migration\ValueObject\Step\UserGroupUpdateStep>>
     */
    public function provideStep(): iterable
    {
        yield 'step with match remoteId' => [
            new UserGroupUpdateStep(
                UpdateMetadata::createFromArray([
                    'remoteId' => self::KNOWN_USER_GROUP_REMOTE_ID,
                    'parentGroupId' => 13,
                    'mainLanguage' => 'eng-GB',
                ]),
                new Matcher('remoteId', self::KNOWN_USER_GROUP_REMOTE_ID),
                [
                    Field::createFromArray([
                        'fieldDefIdentifier' => 'name',
                        'languageCode' => 'eng-GB',
                        'value' => '__user_group_name__',
                    ]),
                ],
                [
                    'Anonymous',
                ]
            ),
        ];

        yield 'step with match id' => [
            new UserGroupUpdateStep(
                UpdateMetadata::createFromArray([
                    'remoteId' => self::KNOWN_USER_GROUP_REMOTE_ID,
                    'parentGroupId' => 13,
                    'mainLanguage' => 'eng-GB',
                ]),
                new Matcher('id', self::KNOWN_USER_GROUP_ID),
                [
                    Field::createFromArray([
                        'fieldDefIdentifier' => 'name',
                        'languageCode' => 'eng-GB',
                        'value' => '__user_group_name__new__',
                    ]),
                ],
                [
                    '1',
                ]
            ),
        ];
    }
}
