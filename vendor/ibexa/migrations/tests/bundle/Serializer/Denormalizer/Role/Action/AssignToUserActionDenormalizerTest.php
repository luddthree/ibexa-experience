<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Denormalizer\Role\Action;

use Ibexa\Bundle\Migration\Serializer\Denormalizer\Role\Action\AssignToUserActionDenormalizer;
use Ibexa\Migration\ValueObject\Step\Action\Role\AssignToUser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Denormalizer\Role\Action\AssignToUserActionDenormalizer
 */
final class AssignToUserActionDenormalizerTest extends TestCase
{
    /**
     * @dataProvider provideForThrowsWhenInvalidMatchingPropertyProvided
     *
     * @param array<string, scalar> $data
     */
    public function testThrowsWhenInvalidMatchingPropertyProvided(array $data, string $expectedExceptionMessage): void
    {
        $denormalizer = new AssignToUserActionDenormalizer();

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        $denormalizer->denormalize($data, AssignToUser::class);
    }

    /**
     * @return iterable<array{array<string, scalar>, string}>
     */
    public function provideForThrowsWhenInvalidMatchingPropertyProvided(): iterable
    {
        yield [
            [],
            'Unable to denormalize "assign_role_to_user" action: missing matching property (one of "id", "login", "email")',
        ];

        yield [
            [
                'id' => 1,
                'login' => 'foo',
            ],
            'Unable to denormalize "assign_role_to_user" action: more than one matching property provided (received: "id", "login")',
        ];

        yield [
            [
                'id' => 1,
                'email' => 'foo',
            ],
            'Unable to denormalize "assign_role_to_user" action: more than one matching property provided (received: "id", "email")',
        ];

        yield [
            [
                'id' => 1,
                'login' => 'foo',
                'email' => 'foo',
            ],
            'Unable to denormalize "assign_role_to_user" action: more than one matching property provided (received: "id", "email", "login")',
        ];

        yield [
            [
                'email' => 1,
                'login' => 'foo',
            ],
            'Unable to denormalize "assign_role_to_user" action: more than one matching property provided (received: "email", "login")',
        ];
    }
}

class_alias(AssignToUserActionDenormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Denormalizer\Role\Action\AssignToUserActionDenormalizerTest');
