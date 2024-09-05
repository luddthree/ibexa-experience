<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\ObjectStateCreateDenormalizer;
use Ibexa\Migration\ValueObject\Step\ObjectStateGroupCreateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Tests\Bundle\Migration\IbexaKernelTestCase;

final class ObjectStateCreateDenormalizerTest extends IbexaKernelTestCase
{
    /** @var \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\ObjectStateCreateDenormalizer */
    private $denormalizer;

    protected function setUp(): void
    {
        $this->denormalizer = new ObjectStateCreateDenormalizer();
    }

    /**
     * @dataProvider provideDenormalize
     *
     * @param array<mixed> $data
     * @param array<mixed> $expectedResult
     */
    public function testDenormalize(array $data, array $expectedResult): void
    {
        self::assertTrue($this->denormalizer->supportsDenormalization($data, StepInterface::class));
        $result = $this->denormalizer->denormalize($data, StepInterface::class);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @return iterable<array{
     *      array<mixed>,
     *      array<mixed>
     * }>
     */
    public function provideDenormalize(): iterable
    {
        $data = [
            'type' => 'object_state',
            'mode' => 'create',
            'identifier' => '__TEST_STATE_ID__',
            'object_state_group' => '__TEST_GROUP_STATE_ID__',
            'names' => [
                'eng-GB' => 'Test name',
            ],
            'descriptions' => [
                'eng-GB' => 'Test desc',
            ],
        ];

        $expectedResult = [
            'type' => 'object_state',
            'mode' => 'create',
            'metadata' => [
                'identifier' => '__TEST_STATE_ID__',
                'mainTranslation' => 'eng-GB',
                'objectStateGroup' => '__TEST_GROUP_STATE_ID__',
                'priority' => false,
                'translations' => [
                    'eng-GB' => [
                        'name' => 'Test name',
                        'description' => 'Test desc',
                    ],
                ],
            ],
        ];

        yield [$data, $expectedResult];

        $dataId = [
            'type' => 'object_state',
            'mode' => 'create',
            'identifier' => '__TEST_STATE_ID__',
            'object_state_group' => 444,
            'names' => [
                'eng-GB' => 'Test name',
            ],
            'descriptions' => [
                'eng-GB' => 'Test desc',
            ],
        ];

        $expectedResult = [
            'type' => 'object_state',
            'mode' => 'create',
            'metadata' => [
                'identifier' => '__TEST_STATE_ID__',
                'mainTranslation' => 'eng-GB',
                'objectStateGroup' => 444,
                'priority' => false,
                'translations' => [
                    'eng-GB' => [
                        'name' => 'Test name',
                        'description' => 'Test desc',
                    ],
                ],
            ],
        ];

        yield [$dataId, $expectedResult];
    }

    public function testSupportsDenormalization(): void
    {
        $supports = $this->denormalizer->supportsDenormalization(null, StepInterface::class);
        self::assertFalse($supports);

        $supports = $this->denormalizer->supportsDenormalization([
            'type' => 'object_state',
            'mode' => 'create',
        ], ObjectStateGroupCreateStep::class);
        self::assertFalse($supports);

        $supports = $this->denormalizer->supportsDenormalization([
            'type' => 'object_state',
            'mode' => 'create',
        ], StepInterface::class);
        self::assertTrue($supports);

        $supports = $this->denormalizer->supportsDenormalization(null, '');
        self::assertFalse($supports);
    }
}

class_alias(ObjectStateCreateDenormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer\ObjectStateCreateDenormalizerTest');
