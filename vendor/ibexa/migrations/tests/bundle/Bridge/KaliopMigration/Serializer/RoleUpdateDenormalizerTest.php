<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\RoleUpdateDenormalizer;
use Ibexa\Migration\ValueObject\Step\RoleUpdateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Tests\Bundle\Migration\IbexaKernelTestCase;

/**
 * @covers \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\RoleUpdateDenormalizer
 */
final class RoleUpdateDenormalizerTest extends IbexaKernelTestCase
{
    /**
     * @dataProvider provideDenormalize
     *
     * @param array<mixed> $data
     * @param array<mixed> $expectedResult
     */
    public function testDenormalize(array $data, array $expectedResult): void
    {
        $denormalizer = new RoleUpdateDenormalizer();

        self::assertTrue($denormalizer->supportsDenormalization($data, StepInterface::class));
        $result = $denormalizer->denormalize($data, StepInterface::class);

        self::assertSame($expectedResult, $result);
    }

    public function testSupportsDenormalization(): void
    {
        $denormalizer = new RoleUpdateDenormalizer();

        $supports = $denormalizer->supportsDenormalization(null, StepInterface::class);
        self::assertFalse($supports);

        $supports = $denormalizer->supportsDenormalization([
            'type' => 'role',
            'mode' => 'update',
        ], RoleUpdateStep::class);
        self::assertFalse($supports);

        $supports = $denormalizer->supportsDenormalization([
            'type' => 'role',
            'mode' => 'update',
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
            'mode' => 'update',
            'new_name' => '__new_name__',
            'role_identifier' => '__new_role_identifier__',
            'policies' => [
                [
                    'module' => '__module__',
                    'function' => '__function__',
                    'limitations' => [
                        [
                            'identifier' => '__limitation_identifier__',
                            'values' => [
                                '__limitation_value_1__',
                                '__limitation_value_2__',
                            ],
                        ],
                    ],
                ],
            ],
            'match' => [
                'identifier' => '__role_identifier__',
            ],
        ];

        $expectedResult = [
            'type' => 'role',
            'mode' => 'update',
            'match' => [
                'field' => 'identifier',
                'value' => '__role_identifier__',
            ],
            'metadata' => [
                'name' => '__new_name__',
            ],
            'policies' => [
                [
                    'module' => '__module__',
                    'function' => '__function__',
                    'limitations' => [
                        [
                            'identifier' => '__limitation_identifier__',
                            'values' => [
                                '__limitation_value_1__',
                                '__limitation_value_2__',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        yield [$data, $expectedResult];

        $data = [
            'type' => 'role',
            'mode' => 'update',
            'new_name' => '__new_name__',
            'role_identifier' => '__new_role_identifier__',
            'match' => [
                'id' => '__id__',
            ],
            'policies' => [
                [
                    'module' => '__module__',
                    'function' => '__function__',
                    'limitations' => [
                        [
                            'identifier' => '__limitation_identifier__',
                            'values' => [
                                '__limitation_value_1__',
                                '__limitation_value_2__',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedResult = [
            'type' => 'role',
            'mode' => 'update',
            'match' => [
                'field' => 'id',
                'value' => '__id__',
            ],
            'metadata' => [
                'name' => '__new_name__',
            ],
            'policies' => [
                [
                    'module' => '__module__',
                    'function' => '__function__',
                    'limitations' => [
                        [
                            'identifier' => '__limitation_identifier__',
                            'values' => [
                                '__limitation_value_1__',
                                '__limitation_value_2__',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        yield [$data, $expectedResult];
    }
}

class_alias(RoleUpdateDenormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer\RoleUpdateDenormalizerTest');
