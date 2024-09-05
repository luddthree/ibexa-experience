<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\SectionUpdateDenormalizer;
use Ibexa\Migration\ValueObject\Step\SectionUpdateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Tests\Bundle\Migration\IbexaKernelTestCase;

final class SectionUpdateDenormalizerTest extends IbexaKernelTestCase
{
    /** @var \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\SectionUpdateDenormalizer */
    private $denormalizer;

    protected function setUp(): void
    {
        $this->denormalizer = new SectionUpdateDenormalizer();
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
            'type' => 'section',
            'mode' => 'update',
            'name' => '__NAME__',
            'identifier' => '__IDENTIFIER__',
            'match' => [
                'identifier' => '__SECTION_IDENTIFIER__',
            ],
        ];

        $expectedResult = [
            'type' => 'section',
            'mode' => 'update',
            'match' => [
                'field' => 'identifier',
                'value' => '__SECTION_IDENTIFIER__',
            ],
            'metadata' => [
                'identifier' => '__IDENTIFIER__',
                'name' => '__NAME__',
            ],
        ];

        yield [$data, $expectedResult];

        $dataWithId = [
            'type' => 'section',
            'mode' => 'update',
            'name' => '__NAME__',
            'identifier' => '__IDENTIFIER__',
            'match' => [
                'id' => 1,
            ],
        ];

        $expectedResult = [
            'type' => 'section',
            'mode' => 'update',
            'match' => [
                'field' => 'id',
                'value' => 1,
            ],
            'metadata' => [
                'identifier' => '__IDENTIFIER__',
                'name' => '__NAME__',
            ],
        ];
        yield [$dataWithId, $expectedResult];
    }

    public function testSupportsDenormalization(): void
    {
        $supports = $this->denormalizer->supportsDenormalization(null, StepInterface::class);
        self::assertFalse($supports);

        $supports = $this->denormalizer->supportsDenormalization([
            'type' => 'section',
            'mode' => 'update',
        ], SectionUpdateStep::class);
        self::assertFalse($supports);

        $supports = $this->denormalizer->supportsDenormalization([
            'type' => 'section',
            'mode' => 'update',
        ], StepInterface::class);
        self::assertTrue($supports);

        $supports = $this->denormalizer->supportsDenormalization(null, '');
        self::assertFalse($supports);
    }
}

class_alias(SectionUpdateDenormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer\SectionUpdateDenormalizerTest');
