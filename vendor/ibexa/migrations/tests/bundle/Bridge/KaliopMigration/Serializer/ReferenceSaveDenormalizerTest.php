<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\ReferenceSaveDenormalizer;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Tests\Bundle\Migration\IbexaKernelTestCase;

/**
 * @covers \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\ReferenceSaveDenormalizer
 */
final class ReferenceSaveDenormalizerTest extends IbexaKernelTestCase
{
    private const REFERENCES_FILES_SUBDIR = '__REFERENCES_FILES_SUBDIR__';

    /** @var \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\ReferenceSaveDenormalizer */
    private $denormalizer;

    protected function setUp(): void
    {
        $this->denormalizer = new ReferenceSaveDenormalizer(self::REFERENCES_FILES_SUBDIR);
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
            'type' => 'reference',
            'mode' => 'save',
            'file' => '__FILENAME__.yaml',
        ];

        $expectedResult = [
            'type' => 'reference',
            'mode' => 'save',
            'filename' => self::REFERENCES_FILES_SUBDIR . '/__FILENAME__.yaml',
        ];

        yield [$data, $expectedResult];
    }

    public function testSupportsDenormalization(): void
    {
        $supports = $this->denormalizer->supportsDenormalization(null, StepInterface::class);
        self::assertFalse($supports);

        $supports = $this->denormalizer->supportsDenormalization([
            'type' => 'reference',
            'mode' => 'save',
        ], ReferenceSaveDenormalizer::class);
        self::assertFalse($supports);

        $supports = $this->denormalizer->supportsDenormalization([
            'type' => 'reference',
            'mode' => 'save',
        ], StepInterface::class);
        self::assertTrue($supports);

        $supports = $this->denormalizer->supportsDenormalization(null, '');
        self::assertFalse($supports);
    }
}

class_alias(ReferenceSaveDenormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer\ReferenceSaveDenormalizerTest');
