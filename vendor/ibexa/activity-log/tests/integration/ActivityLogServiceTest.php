<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ActivityLog;

use DateTimeImmutable;
use Ibexa\Contracts\ActivityLog\ActivityLogServiceInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActionInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityLogGroupInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityLogInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\ObjectCriterion;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ObjectClassInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Query;
use Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException;
use Ibexa\Contracts\Test\Core\IbexaKernelTestCase;
use Ibexa\Tests\Integration\ActivityLog\Security\PermissionResolverMock;
use PHPUnit\Framework\ExpectationFailedException;

final class ActivityLogServiceTest extends IbexaKernelTestCase
{
    private ActivityLogServiceInterface $activityLogService;

    protected function setUp(): void
    {
        self::bootKernel();
        $ibexaTestCore = $this->getIbexaTestCore();
        $this->activityLogService = $ibexaTestCore->getServiceByClassName(ActivityLogServiceInterface::class);
    }

    public function testListActivityWithoutPermission(): void
    {
        $this->setForbiddenToViewActivityLogs();

        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('security.activity_log.access_forbidden');

        $this->activityLogService->countGroups();
    }

    public function testListActivity(): void
    {
        $this->setPermittedToViewActivityLogs();

        self::assertSame(5, $this->activityLogService->countGroups());
    }

    /**
     * @depends testListActivity
     */
    public function testSavingActivity(): void
    {
        $this->setPermittedToViewActivityLogs();
        self::assertSame(5, $this->activityLogService->countGroups());

        $logFoo = $this->activityLogService->build('stdClass', '123', 'some_action');
        $logFoo->setObjectName('foo_name');
        $logBar = $this->activityLogService->build('stdClass', '456', 'some_other_action');
        $this->activityLogService->save($logFoo);
        $this->activityLogService->save($logBar);

        self::assertSame(7, $this->activityLogService->countGroups());

        $query = new Query([
            new ObjectCriterion('stdClass', ['123', '456']),
        ]);
        $activityList = $this->activityLogService->findGroups($query)->getActivityLogs();
        self::assertCount(2, $activityList);

        $constraint = self::logicalAnd(
            self::isInstanceOf(ActivityLogGroupInterface::class),
            self::logicalXor(
                self::callback(static function (ActivityLogGroupInterface $activityLogGroup) use ($logFoo): bool {
                    try {
                        $activityLogs = $activityLogGroup->getActivityLogs();
                        self::assertCount(1, $activityLogs);
                        $activityLog = $activityLogs[0];
                        self::assertInstanceOf(ActivityLogInterface::class, $activityLog);
                        self::assertInstanceOf(DateTimeImmutable::class, $activityLogGroup->getLoggedAt());
                        self::assertSame($logFoo->getObjectClass(), $activityLog->getObjectClass());
                        self::assertSame($logFoo->getObjectName(), $activityLog->getObjectName());
                        self::assertSame($logFoo->getObjectId(), $activityLog->getObjectId());
                        self::assertSame($logFoo->getAction(), $activityLog->getAction());
                    } catch (ExpectationFailedException $e) {
                        return false;
                    }

                    return true;
                }),
                self::callback(static function (ActivityLogGroupInterface $activityLogGroup) use ($logBar): bool {
                    try {
                        $activityLogs = $activityLogGroup->getActivityLogs();
                        self::assertCount(1, $activityLogs);
                        $activityLog = $activityLogs[0];
                        self::assertInstanceOf(ActivityLogInterface::class, $activityLog);
                        self::assertInstanceOf(DateTimeImmutable::class, $activityLogGroup->getLoggedAt());
                        self::assertSame($logBar->getObjectClass(), $activityLog->getObjectClass());
                        self::assertNull($activityLog->getObjectName());
                        self::assertSame($logBar->getObjectId(), $activityLog->getObjectId());
                        self::assertSame($logBar->getAction(), $activityLog->getAction());
                    } catch (ExpectationFailedException $e) {
                        return false;
                    }

                    return true;
                }),
            ),
        );

        foreach ($activityList as $activityLog) {
            self::assertThat($activityLog, $constraint);
        }
    }

    public function testListObjectClasses(): void
    {
        $objectClasses = $this->activityLogService->getObjectClasses();

        self::assertContainsOnlyInstancesOf(ObjectClassInterface::class, $objectClasses);
        $results = array_map(
            static function (ObjectClassInterface $objectClass): array {
                return [
                    'class_name' => $objectClass->getObjectClass(),
                    'short_name' => $objectClass->getShortName(),
                ];
            },
            $objectClasses,
        );

        self::assertEqualsCanonicalizing(
            [
                [
                    'class_name' => 'stdClass',
                    'short_name' => null,
                ],
                [
                    'class_name' => 'otherClass',
                    'short_name' => null,
                ],
            ],
            $results,
        );
    }

    public function testListActions(): void
    {
        $actions = $this->activityLogService->getActions();
        $actions = array_map(
            static fn (ActionInterface $action): string => $action->getName(),
            $actions,
        );

        self::assertEqualsCanonicalizing(['foo_action', 'bar_action'], $actions);
    }

    /**
     * @depends testSavingActivity
     */
    public function testSavingGroup(): void
    {
        $this->setPermittedToViewActivityLogs();
        self::assertSame(5, $this->activityLogService->countGroups());

        $this->activityLogService->prepareContext('admin_ui', 'operation_description');

        $logFoo = $this->activityLogService->build('stdClass', '123', 'some_action');
        $logFoo->setObjectName('foo_name');
        $logBar = $this->activityLogService->build('stdClass', '456', 'some_other_action');
        $fooId = $this->activityLogService->save($logFoo);
        $barId = $this->activityLogService->save($logBar);

        self::assertNotNull($fooId);
        self::assertNotNull($barId);
    }

    private function setPermittedToViewActivityLogs(): void
    {
        $permissionResolverMock = $this->getIbexaTestCore()->getServiceByClassName(PermissionResolverMock::class);
        $permissionResolverMock->setHasAccessResult(true);
    }

    private function setForbiddenToViewActivityLogs(): void
    {
        $permissionResolverMock = $this->getIbexaTestCore()->getServiceByClassName(PermissionResolverMock::class);
        $permissionResolverMock->setHasAccessResult(false);
    }
}
