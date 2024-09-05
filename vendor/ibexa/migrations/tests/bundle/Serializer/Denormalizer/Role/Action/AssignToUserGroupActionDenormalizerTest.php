<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Denormalizer\Role\Action;

use Ibexa\Bundle\Migration\Serializer\Denormalizer\Role\Action\AssignToUserGroupActionDenormalizer;
use Ibexa\Migration\ValueObject\Step\Action\Role\AssignToUserGroup;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Denormalizer\Role\Action\AssignToUserGroupActionDenormalizer
 */
final class AssignToUserGroupActionDenormalizerTest extends TestCase
{
    /**
     * @dataProvider provideForThrowsWhenInvalidMatchingPropertyProvided
     *
     * @param array<string, scalar> $data
     */
    public function testThrowsWhenInvalidMatchingPropertyProvided(array $data, string $expectedExceptionMessage): void
    {
        $denormalizer = new AssignToUserGroupActionDenormalizer();

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        $denormalizer->denormalize($data, AssignToUserGroup::class);
    }

    /**
     * @return iterable<array{array<string, scalar>, string}>
     */
    public function provideForThrowsWhenInvalidMatchingPropertyProvided(): iterable
    {
        yield [
            [],
            'Unable to denormalize "assign_role_to_user_group" action: missing matching property (one of "id", "remote_id")',
        ];

        yield [
            [
                'id' => 1,
                'remote_id' => 'foo',
            ],
            'Unable to denormalize "assign_role_to_user_group" action: more than one matching property provided (received: "id", "remote_id")',
        ];
    }
}

class_alias(AssignToUserGroupActionDenormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Denormalizer\Role\Action\AssignToUserGroupActionDenormalizerTest');
