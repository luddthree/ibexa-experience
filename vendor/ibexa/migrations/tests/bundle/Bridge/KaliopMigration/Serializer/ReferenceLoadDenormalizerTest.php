<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\Reference\ReferenceFileConverter;
use Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\ReferenceLoadDenormalizer;
use Ibexa\Migration\ValueObject\Step\ReferenceLoadStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Tests\Bundle\Migration\IbexaKernelTestCase;

/**
 * @covers \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\ReferenceLoadDenormalizer
 */
final class ReferenceLoadDenormalizerTest extends IbexaKernelTestCase
{
    /** @var \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\ReferenceLoadDenormalizer */
    private $denormalizer;

    /** @var \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\Reference\ReferenceFileConverter|\PHPUnit\Framework\MockObject\MockObject */
    private $referenceFileConverter;

    protected function setUp(): void
    {
        $this->referenceFileConverter = $this->createMock(ReferenceFileConverter::class);

        $this->denormalizer = new ReferenceLoadDenormalizer($this->referenceFileConverter);
    }

    /**
     * @dataProvider provideDenormalize
     *
     * @param array<mixed> $data
     * @param array<mixed> $expectedResult
     */
    public function testDenormalize(array $data, array $expectedResult): void
    {
        $this->referenceFileConverter
            ->method('convert')
            ->with('__FILENAME__', '__OUTPUT__')
            ->willReturn('__CONVERTED_FILENAME__');

        self::assertTrue($this->denormalizer->supportsDenormalization($data, StepInterface::class));
        $result = $this->denormalizer->denormalize($data, StepInterface::class, null, [
            'output' => '__OUTPUT__',
        ]);
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
            'type' => 'reference',
            'mode' => 'load',
            'file' => '__FILENAME__',
        ];

        $expectedResult = [
            'type' => 'reference',
            'mode' => 'load',
            'filename' => '__CONVERTED_FILENAME__',
        ];

        yield [$data, $expectedResult];
    }

    public function testSupportsDenormalization(): void
    {
        $supports = $this->denormalizer->supportsDenormalization(null, StepInterface::class);
        self::assertFalse($supports);

        $supports = $this->denormalizer->supportsDenormalization([
            'type' => 'reference',
            'mode' => 'load',
        ], ReferenceLoadStep::class);
        self::assertFalse($supports);

        $supports = $this->denormalizer->supportsDenormalization([
            'type' => 'reference',
            'mode' => 'load',
        ], StepInterface::class);
        self::assertTrue($supports);

        $supports = $this->denormalizer->supportsDenormalization(null, '');
        self::assertFalse($supports);
    }
}

class_alias(ReferenceLoadDenormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer\ReferenceLoadDenormalizerTest');
