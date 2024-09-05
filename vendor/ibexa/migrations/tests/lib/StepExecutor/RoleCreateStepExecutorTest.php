<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\StepExecutor;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\OwnerLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Migration\Reference\ResolverInterface;
use Ibexa\Migration\StepExecutor\RoleCreateStepExecutor;
use Ibexa\Migration\ValueObject\Step\Role\CreateMetadata;
use Ibexa\Migration\ValueObject\Step\Role\Limitation;
use Ibexa\Migration\ValueObject\Step\Role\Policy;
use Ibexa\Migration\ValueObject\Step\RoleCreateStep;

/**
 * @covers \Ibexa\Migration\StepExecutor\RoleCreateStepExecutor
 */
final class RoleCreateStepExecutorTest extends AbstractRoleStepExecutorTest
{
    public function testHandle(): void
    {
        $role = $this->findRoleCreatedByTest();
        self::assertNull($role);

        $executor = new RoleCreateStepExecutor(
            self::getTransactionHandler(),
            self::getRoleService(),
            self::getLimitationConverter(),
            self::getRoleActionExecutor(),
        );

        $this->configureExecutor($executor, [
            ResolverInterface::class => self::getReferenceResolver('role'),
        ]);

        $step = $this->createStep();
        $executor->handle($step);

        $role = $this->findRoleCreatedByTest();
        self::assertInstanceOf(Role::class, $role);
        self::assertSame('__roleIdentifier__', $role->identifier);
        self::assertCount(1, $role->policies);
        $policy = $role->policies[0];
        self::assertSame('content', $policy->module);
        self::assertSame('read', $policy->function);
        self::assertCount(1, $policy->limitations);
        $limitation = $policy->limitations[0];
        self::assertInstanceOf(OwnerLimitation::class, $limitation);
        self::assertSame(['1'], $limitation->limitationValues);
    }

    private function findRoleCreatedByTest(): ?Role
    {
        try {
            return self::getRoleService()->loadRoleByIdentifier('__roleIdentifier__');
        } catch (NotFoundException $e) {
            return null;
        }
    }

    private function createStep(): RoleCreateStep
    {
        return new RoleCreateStep(
            new CreateMetadata('__roleIdentifier__'),
            [
                new Policy(
                    'content',
                    'read',
                    [
                        new Limitation('Owner', [1]),
                    ]
                ),
            ],
        );
    }
}

class_alias(RoleCreateStepExecutorTest::class, 'Ibexa\Platform\Tests\Migration\StepExecutor\RoleCreateStepExecutorTest');
