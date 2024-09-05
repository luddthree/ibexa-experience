<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\CorporateAccount\Validation\Builder;

use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\CorporateAccount\Validation\Builder\MemberRoleValidatorBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ValidatorBuilder;

final class MemberRoleValidatorBuilderTest extends TestCase
{
    private const ALLOWED_ROLE_IDENTIFIERS = [
        'admin' => 'Company admin',
        'buyer' => 'Company buyer',
    ];

    private MemberRoleValidatorBuilder $builder;

    /**
     * @return iterable<string, array{\Ibexa\Contracts\Core\Repository\Values\User\Role, array<string>}>
     */
    public function getDataForTestValidateRoleIdentifier(): iterable
    {
        yield 'admin' => [$this->createRoleMock('Company admin'), []];
        yield 'buyer' => [$this->createRoleMock('Company buyer'), []];
        yield 'editor' => [
            $this->createRoleMock('Editor'),
            ['"Editor" is not a valid Corporate Account Role. Use one of the following instead: "Company admin", "Company buyer"'],
        ];
    }

    protected function setUp(): void
    {
        $this->builder = new MemberRoleValidatorBuilder(
            (new ValidatorBuilder())->getValidator()
        );
    }

    /**
     * @dataProvider getDataForTestValidateRoleIdentifier
     *
     * @param array<string> $expectedViolationMessages
     */
    public function testValidateRoleIdentifier(Role $role, array $expectedViolationMessages): void
    {
        $this->builder->validateRole($role, self::ALLOWED_ROLE_IDENTIFIERS);
        $actualViolationMessages = array_map(
            static fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
            iterator_to_array($this->builder->build()->getViolations())
        );

        self::assertEqualsCanonicalizing($expectedViolationMessages, $actualViolationMessages);
    }

    private function createRoleMock(string $identifier): Role
    {
        $roleMock = $this->createMock(Role::class);
        $roleMock
            ->method('__get')
            ->with('identifier')
            ->willReturn($identifier);

        return $roleMock;
    }
}
