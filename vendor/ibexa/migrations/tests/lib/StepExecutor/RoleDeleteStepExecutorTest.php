<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\StepExecutor;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Migration\StepExecutor\RoleDeleteStepExecutor;
use Ibexa\Migration\ValueObject\Step\Role\Matcher;
use Ibexa\Migration\ValueObject\Step\RoleDeleteStep;

/**
 * @covers \Ibexa\Migration\StepExecutor\RoleDeleteStepExecutor
 */
final class RoleDeleteStepExecutorTest extends AbstractRoleStepExecutorTest
{
    private const KNOWN_FIXTURE_ROLE_IDENTIFIER = 'Editor';
    private const KNOWN_FIXTURE_ROLE_ID = 3;

    /**
     * @dataProvider provideSteps
     */
    public function testHandle(RoleDeleteStep $step): void
    {
        $role = $this->findRole(self::KNOWN_FIXTURE_ROLE_IDENTIFIER);
        self::assertNotNull($role);

        $executor = new RoleDeleteStepExecutor(self::getTransactionHandler(), self::getRoleService());

        self::assertTrue($executor->canHandle($step));
        $executor->handle($step);

        $role = $this->findRole(self::KNOWN_FIXTURE_ROLE_IDENTIFIER);
        self::assertNull($role);
    }

    private function findRole(string $identifier): ?Role
    {
        try {
            return self::getRoleService()->loadRoleByIdentifier($identifier);
        } catch (NotFoundException $e) {
            return null;
        }
    }

    /**
     * @return iterable<array{\Ibexa\Migration\ValueObject\Step\RoleDeleteStep}>
     */
    public function provideSteps(): iterable
    {
        yield [
            new RoleDeleteStep(
                new Matcher('identifier', self::KNOWN_FIXTURE_ROLE_IDENTIFIER)
            ),
        ];

        yield [
            new RoleDeleteStep(
                new Matcher('id', self::KNOWN_FIXTURE_ROLE_ID)
            ),
        ];
    }
}

class_alias(RoleDeleteStepExecutorTest::class, 'Ibexa\Platform\Tests\Migration\StepExecutor\RoleDeleteStepExecutorTest');
