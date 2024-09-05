<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\SQLExecuteDenormalizer;
use Ibexa\Migration\ValueObject\Step\SectionUpdateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Tests\Bundle\Migration\IbexaKernelTestCase;

final class SQLExecuteDenormalizerTest extends IbexaKernelTestCase
{
    /** @var \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\SQLExecuteDenormalizer */
    private $denormalizer;

    protected function setUp(): void
    {
        $this->denormalizer = new SQLExecuteDenormalizer();
    }

    /**
     * @dataProvider provideDenormalize
     *
     * @param array<mixed> $data
     * @param array<mixed> $expectedResult
     */
    public function testDenormalize(array $data, array $expectedResult): void
    {
        $supports = $this->denormalizer->supportsDenormalization($data, StepInterface::class);
        self::assertTrue($supports);

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
            'type' => 'sql',
            'mysql' => '__QUERY__',
        ];

        $expectedResult = [
            'type' => 'sql',
            'mode' => 'execute',
            'query' => [
                [
                    'driver' => 'mysql',
                    'sql' => '__QUERY__',
                ],
            ],
        ];

        yield [$data, $expectedResult];

        $data = [
            'type' => 'sql',
            'mysql' => '__QUERY__',
            'sqlite' => '__QUERY_SQLITE__',
            'mode' => 'exec',
        ];

        $expectedResult = [
            'type' => 'sql',
            'mode' => 'execute',
            'query' => [
                [
                    'driver' => 'mysql',
                    'sql' => '__QUERY__',
                ],
                [
                    'driver' => 'sqlite',
                    'sql' => '__QUERY_SQLITE__',
                ],
            ],
        ];

        yield [$data, $expectedResult];
    }

    public function testSupportsDenormalization(): void
    {
        $supports = $this->denormalizer->supportsDenormalization(null, StepInterface::class);
        self::assertFalse($supports);

        $supports = $this->denormalizer->supportsDenormalization([
            'type' => 'sql',
            'mode' => 'exec',
        ], SectionUpdateStep::class);
        self::assertFalse($supports);

        $supports = $this->denormalizer->supportsDenormalization([
            'type' => 'sql',
            'mode' => 'exec',
        ], StepInterface::class);
        self::assertTrue($supports);

        $supports = $this->denormalizer->supportsDenormalization(null, '');
        self::assertFalse($supports);

        $supports = $this->denormalizer->supportsDenormalization([
            'type' => 'sql',
        ], StepInterface::class);
        self::assertTrue($supports);
    }
}

class_alias(SQLExecuteDenormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer\SQLExecuteDenormalizerTest');
