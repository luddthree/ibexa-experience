<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer\Action;

use Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\Action\AbstractUserRolesActionDenormalizer;
use Ibexa\Migration\ValueObject\Step\Action;
use PHPUnit\Framework\TestCase;

abstract class AbstractUserRolesActionDenormalizerTest extends TestCase
{
    /** @var \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\Action\AbstractUserRolesActionDenormalizer */
    private $denormalizer;

    protected function setUp(): void
    {
        $this->denormalizer = $this->getDenormalizer();
    }

    abstract protected function createDenormalizer(): AbstractUserRolesActionDenormalizer;

    abstract protected function getActionType(): string;

    /** @return class-string<\Ibexa\Migration\ValueObject\Step\Action\AbstractUserAssignRole> */
    abstract protected function getActionClass(): string;

    protected function getDenormalizer(): AbstractUserRolesActionDenormalizer
    {
        if (!isset($this->denormalizer)) {
            $this->denormalizer = $this->createDenormalizer();
        }

        return $this->denormalizer;
    }

    final public function testSupports(): void
    {
        self::assertTrue($this->denormalizer->supportsDenormalization(null, $this->getActionClass()));
        self::assertFalse($this->denormalizer->supportsDenormalization(null, Action::class));
    }

    /**
     * @param array<scalar> $roles
     * @param array<array<string, scalar>> $expectedResult
     *
     * @dataProvider provideForConversion
     */
    final public function testConversion(array $roles, array $expectedResult): void
    {
        self::assertSame($expectedResult, $this->denormalizer->denormalize($roles, $this->getActionClass()));
    }

    /**
     * @return iterable<string, array{array<scalar>, array<array<string, scalar>>}>
     */
    final public function provideForConversion(): iterable
    {
        yield "Role are given as ID's" => [
            [
                1,
                2,
                3,
            ], [
                [
                    'action' => $this->getActionType(),
                    'id' => 1,
                ], [
                    'action' => $this->getActionType(),
                    'id' => 2,
                ], [
                    'action' => $this->getActionType(),
                    'id' => 3,
                ],
            ],
        ];

        yield 'Roles are given as identifiers' => [
            [
                'foo',
                'bar',
            ], [
                [
                    'action' => $this->getActionType(),
                    'identifier' => 'foo',
                ], [
                    'action' => $this->getActionType(),
                    'identifier' => 'bar',
                ],
            ],
        ];
    }
}

class_alias(AbstractUserRolesActionDenormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer\Action\AbstractUserRolesActionDenormalizerTest');
