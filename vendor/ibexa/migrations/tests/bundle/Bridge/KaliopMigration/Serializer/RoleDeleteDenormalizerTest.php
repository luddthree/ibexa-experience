<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\RoleDeleteDenormalizer;
use Ibexa\Migration\ValueObject\Step\RoleDeleteStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Tests\Bundle\Migration\IbexaKernelTestCase;

/**
 * @covers \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\RoleDeleteDenormalizer
 */
final class RoleDeleteDenormalizerTest extends IbexaKernelTestCase
{
    /**
     * @dataProvider provideDenormalize
     *
     * @param array<mixed> $data
     * @param array<mixed> $expectedResult
     */
    public function testDenormalize(array $data, array $expectedResult): void
    {
        $denormalizer = new RoleDeleteDenormalizer();

        self::assertTrue($denormalizer->supportsDenormalization($data, StepInterface::class));
        $result = $denormalizer->denormalize($data, StepInterface::class);

        self::assertSame($expectedResult, $result);
    }

    public function testSupportsDenormalization(): void
    {
        $denormalizer = new RoleDeleteDenormalizer();

        $supports = $denormalizer->supportsDenormalization(null, StepInterface::class);
        self::assertFalse($supports);

        $supports = $denormalizer->supportsDenormalization([
            'type' => 'role',
            'mode' => 'delete',
        ], RoleDeleteStep::class);
        self::assertFalse($supports);

        $supports = $denormalizer->supportsDenormalization([
            'type' => 'role',
            'mode' => 'delete',
        ], StepInterface::class);
        self::assertTrue($supports);

        $supports = $denormalizer->supportsDenormalization(null, '');
        self::assertFalse($supports);
    }

    /**
     * @return iterable<array{
     *      array<mixed>,
     *      array<mixed>,
     * }>
     */
    public function provideDenormalize(): iterable
    {
        $data = [
            'type' => 'role',
            'mode' => 'delete',
            'match' => [
                'identifier' => '__role_identifier__',
            ],
        ];

        $expectedResult = [
            'type' => 'role',
            'mode' => 'delete',
            'match' => [
                'field' => 'identifier',
                'value' => '__role_identifier__',
            ],
        ];

        yield [$data, $expectedResult];

        $data = [
            'type' => 'role',
            'mode' => 'delete',
            'match' => [
                'id' => '__id__',
            ],
        ];

        $expectedResult = [
            'type' => 'role',
            'mode' => 'delete',
            'match' => [
                'field' => 'id',
                'value' => '__id__',
            ],
        ];

        yield [$data, $expectedResult];
    }
}

class_alias(RoleDeleteDenormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer\RoleDeleteDenormalizerTest');
