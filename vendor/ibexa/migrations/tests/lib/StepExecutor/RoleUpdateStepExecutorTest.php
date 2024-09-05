<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\StepExecutor;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Migration\Reference\ResolverInterface;
use Ibexa\Migration\StepExecutor\RoleUpdateStepExecutor;
use Ibexa\Migration\ValueObject\Step\Role\Limitation;
use Ibexa\Migration\ValueObject\Step\Role\Matcher;
use Ibexa\Migration\ValueObject\Step\Role\Policy;
use Ibexa\Migration\ValueObject\Step\Role\PolicyList;
use Ibexa\Migration\ValueObject\Step\Role\UpdateMetadata;
use Ibexa\Migration\ValueObject\Step\RoleUpdateStep;

/**
 * @covers \Ibexa\Migration\StepExecutor\RoleUpdateStepExecutor
 */
final class RoleUpdateStepExecutorTest extends AbstractRoleStepExecutorTest
{
    private const KNOWN_FIXTURE_ROLE_IDENTIFIER = 'Editor';
    private const KNOWN_FIXTURE_ROLE_ID = 3;
    private const NEW_ROLE_IDENTIFIER = '__new_role_identifier__';

    /**
     * @dataProvider provideSteps
     */
    public function testHandle(RoleUpdateStep $step): void
    {
        $role = $this->findRole(self::KNOWN_FIXTURE_ROLE_IDENTIFIER);
        self::assertNotNull($role);
        self::assertCount(28, $role->policies);

        $executor = new RoleUpdateStepExecutor(
            self::getTransactionHandler(),
            self::getRoleService(),
            self::getLimitationConverter(),
            self::getRoleActionExecutor(),
        );

        $this->configureExecutor($executor, [
            ResolverInterface::class => self::getReferenceResolver('role'),
        ]);

        self::assertTrue($executor->canHandle($step));
        $executor->handle($step);

        $role = $this->findRole(self::NEW_ROLE_IDENTIFIER);
        self::assertNotNull($role);
        self::assertCount(1, $role->policies);
        $policy = $role->policies[0];
        self::assertCount(1, $policy->limitations);
        $limitation = $policy->limitations[0];
        self::assertSame('Owner', $limitation->getIdentifier());
        self::assertSame(['1'], $limitation->limitationValues);
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
     * @return iterable<array{\Ibexa\Migration\ValueObject\Step\RoleUpdateStep}>
     */
    public function provideSteps(): iterable
    {
        yield 'Match by identifier' => [
            new RoleUpdateStep(
                new Matcher(Matcher::IDENTIFIER, self::KNOWN_FIXTURE_ROLE_IDENTIFIER),
                new UpdateMetadata(self::NEW_ROLE_IDENTIFIER),
                new PolicyList([
                    new Policy(
                        'content',
                        'read',
                        [
                            new Limitation('Owner', [1]),
                        ]
                    ),
                ]),
            ),
        ];

        yield 'Match by ID' => [
            new RoleUpdateStep(
                new Matcher(Matcher::ID, self::KNOWN_FIXTURE_ROLE_ID),
                new UpdateMetadata(self::NEW_ROLE_IDENTIFIER),
                new PolicyList([
                    new Policy(
                        'content',
                        'read',
                        [
                            new Limitation('Owner', [1]),
                        ]
                    ),
                ]),
            ),
        ];
    }
}

class_alias(RoleUpdateStepExecutorTest::class, 'Ibexa\Platform\Tests\Migration\StepExecutor\RoleUpdateStepExecutorTest');
