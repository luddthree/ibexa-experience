<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\RoleCreateDenormalizer;
use Ibexa\Migration\ValueObject\Step\RoleCreateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Tests\Bundle\Migration\IbexaKernelTestCase;

/**
 * @covers \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\RoleCreateDenormalizer
 */
final class RoleCreateDenormalizerTest extends IbexaKernelTestCase
{
    /**
     * @dataProvider provideDenormalize
     *
     * @param array<mixed> $data
     * @param array<mixed> $expectedResult
     */
    public function testDenormalize(array $data, array $expectedResult): void
    {
        $denormalizer = new RoleCreateDenormalizer();

        self::assertTrue($denormalizer->supportsDenormalization($data, StepInterface::class));
        $result = $denormalizer->denormalize($data, StepInterface::class);

        self::assertSame($expectedResult, $result);
    }

    public function testSupportsDenormalization(): void
    {
        $denormalizer = new RoleCreateDenormalizer();

        $supports = $denormalizer->supportsDenormalization(null, StepInterface::class);
        self::assertFalse($supports);

        $supports = $denormalizer->supportsDenormalization([
            'type' => 'role',
            'mode' => 'create',
        ], RoleCreateStep::class);
        self::assertFalse($supports);

        $supports = $denormalizer->supportsDenormalization([
            'type' => 'role',
            'mode' => 'create',
        ], StepInterface::class);
        self::assertTrue($supports);

        $supports = $denormalizer->supportsDenormalization(null, '');
        self::assertFalse($supports);
    }

    /**
     * @return iterable<string, array{
     *      array<mixed>,
     *      array<mixed>,
     * }>
     */
    public function provideDenormalize(): iterable
    {
        $data = [
            'type' => 'role',
            'mode' => 'create',
            'name' => '__role_identifier__',
            'role_identifier' => '__role_identifier__',
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
            'mode' => 'create',
            'metadata' => [
                'identifier' => '__role_identifier__',
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

        yield 'simple role creation' => [
            $data,
            $expectedResult,
        ];

        yield 'role creation with user group assignment' => [
            $data + [
                'assign' => [
                    [
                        'type' => 'group',
                        'ids' => [42, 'foo'],
                    ],
                ],
            ],
            $expectedResult + [
                'actions' => [
                    [
                        'action' => 'assign_role_to_user_group',
                        'id' => 42,
                    ], [
                        'action' => 'assign_role_to_user_group',
                        'id' => 'foo',
                    ],
                ],
            ],
        ];

        yield 'role creation with user assignment' => [
            $data + [
                'assign' => [
                    [
                        'type' => 'user',
                        'ids' => [42, 'foo'],
                    ],
                ],
            ],
            $expectedResult + [
                'actions' => [
                    [
                        'action' => 'assign_role_to_user',
                        'id' => 42,
                    ], [
                        'action' => 'assign_role_to_user',
                        'id' => 'foo',
                    ],
                ],
            ],
        ];

        yield 'role creation with user group assignment and ids as single scalar' => [
            $data + [
                'assign' => [
                    [
                        'type' => 'group',
                        'ids' => 42,
                    ],
                ],
            ],
            $expectedResult + [
                'actions' => [
                    [
                        'action' => 'assign_role_to_user_group',
                        'id' => 42,
                    ],
                ],
            ],
        ];

        yield 'role creation with user assignment and ids as single scalar' => [
            $data + [
                'assign' => [
                    [
                        'type' => 'user',
                        'ids' => 42,
                    ],
                ],
            ],
            $expectedResult + [
                'actions' => [
                    [
                        'action' => 'assign_role_to_user',
                        'id' => 42,
                    ],
                ],
            ],
        ];
    }
}

class_alias(RoleCreateDenormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer\RoleCreateDenormalizerTest');
