<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Denormalizer\Action;

use Ibexa\Bundle\Migration\Serializer\Denormalizer\Action\AssignRoleActionDenormalizer;
use Ibexa\Migration\ValueObject\Step\Action\AbstractUserAssignRole;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Denormalizer\Action\AssignRoleActionDenormalizer
 */
final class AssignRoleActionDenormalizerTest extends TestCase
{
    /**
     * @dataProvider provideForThrowsWhenInvalidMatchingPropertyProvided
     *
     * @param array<string, scalar> $data
     */
    public function testThrowsWhenInvalidMatchingPropertyProvided(array $data, string $expectedExceptionMessage): void
    {
        $denormalizer = new AssignRoleActionDenormalizer();

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        $denormalizer->denormalize($data, AbstractUserAssignRole::class);
    }

    /**
     * @return iterable<array{array<string, scalar>, string}>
     */
    public function provideForThrowsWhenInvalidMatchingPropertyProvided(): iterable
    {
        yield [
            [
                'action' => 'foo',
            ],
            'Unable to denormalize "foo" action: missing matching property (one of "id", "identifier")',
        ];

        yield [
            [
                'action' => 'foo',
                'id' => 1,
                'identifier' => 'foo',
            ],
            'Unable to denormalize "foo" action: more than one matching property provided (received: "id", "identifier")',
        ];
    }
}

class_alias(AssignRoleActionDenormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Denormalizer\Action\AssignRoleActionDenormalizerTest');
