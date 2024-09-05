<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\Generator\Role;

use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Core\Repository\Values\User\Role;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\Role\RoleMigrationGenerator;
use Ibexa\Migration\Generator\StepBuilder\StepFactoryInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use PHPUnit\Framework\TestCase;
use Traversable;

/**
 * @covers \Ibexa\Migration\Generator\Role\RoleMigrationGenerator
 */
final class RoleMigrationGeneratorTest extends TestCase
{
    public const MOCK_ROLE_ID = 42;

    public function testGenerateAllRoles(): void
    {
        $context = [
            'value' => ['*'],
        ];
        $roleMock = new Role([]);
        $stepFactory = $this->createStepFactoryMock($roleMock);

        $roleService = $this->createMock(RoleService::class);
        $roleService->expects(self::atLeastOnce())
            ->method('loadRoles')
            ->willReturn([$roleMock]);

        $generator = new RoleMigrationGenerator($stepFactory, $roleService);

        $values = $generator->generate(new Mode('create'), $context);
        self::assertInstanceOf(Traversable::class, $values);
        self::assertContainsOnlyInstancesOf(StepInterface::class, $values);
    }

    public function testGenerateRolesByIdentifier(): void
    {
        $context = [
            'match-property' => 'identifier',
            'value' => ['__identifier__'],
        ];
        $roleMock = new Role([]);
        $stepFactory = $this->createStepFactoryMock($roleMock);

        $roleService = $this->createMock(RoleService::class);
        $roleService->expects(self::atLeastOnce())
            ->method('loadRoleByIdentifier')
            ->with('__identifier__')
            ->willReturn($roleMock);

        $generator = new RoleMigrationGenerator($stepFactory, $roleService);

        $values = $generator->generate(new Mode('create'), $context);
        self::assertInstanceOf(Traversable::class, $values);
        self::assertContainsOnlyInstancesOf(StepInterface::class, $values);
    }

    public function testGenerateRolesById(): void
    {
        $context = [
            'match-property' => 'id',
            'value' => [self::MOCK_ROLE_ID],
        ];
        $roleMock = new Role([]);
        $stepFactory = $this->createStepFactoryMock($roleMock);

        $roleService = $this->createMock(RoleService::class);
        $roleService->expects(self::atLeastOnce())
            ->method('loadRole')
            ->with(self::MOCK_ROLE_ID)
            ->willReturn($roleMock);

        $generator = new RoleMigrationGenerator($stepFactory, $roleService);

        $values = $generator->generate(new Mode('create'), $context);
        self::assertInstanceOf(Traversable::class, $values);
        self::assertContainsOnlyInstancesOf(StepInterface::class, $values);
    }

    private function createStepFactoryMock(Role $roleMock): StepFactoryInterface
    {
        $stepMock = $this->createMock(StepInterface::class);
        $stepFactory = $this->createMock(StepFactoryInterface::class);
        $stepFactory->expects(self::atLeastOnce())
            ->method('create')
            ->with($roleMock)
            ->willReturn($stepMock);

        return $stepFactory;
    }
}

class_alias(RoleMigrationGeneratorTest::class, 'Ibexa\Platform\Tests\Migration\Generator\Role\RoleMigrationGeneratorTest');
