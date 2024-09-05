<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\SectionCreateDenormalizer;
use Ibexa\Migration\ValueObject\Step\SectionCreateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Tests\Bundle\Migration\IbexaKernelTestCase;

final class SectionCreateDenormalizerTest extends IbexaKernelTestCase
{
    /** @var \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\SectionCreateDenormalizer */
    private $denormalizer;

    protected function setUp(): void
    {
        $this->denormalizer = new SectionCreateDenormalizer();
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
            'mode' => 'create',
            'name' => '__NAME__',
            'identifier' => '__IDENTIFIER__',
        ];

        $expectedResult = [
            'type' => 'section',
            'mode' => 'create',
            'metadata' => [
                'identifier' => '__IDENTIFIER__',
                'name' => '__NAME__',
            ],
        ];

        yield [$data, $expectedResult];
    }

    public function testSupportsDenormalization(): void
    {
        $supports = $this->denormalizer->supportsDenormalization(null, StepInterface::class);
        self::assertFalse($supports);

        $supports = $this->denormalizer->supportsDenormalization([
            'type' => 'section',
            'mode' => 'create',
        ], SectionCreateStep::class);
        self::assertFalse($supports);

        $supports = $this->denormalizer->supportsDenormalization([
            'type' => 'section',
            'mode' => 'create',
        ], StepInterface::class);
        self::assertTrue($supports);

        $supports = $this->denormalizer->supportsDenormalization(null, '');
        self::assertFalse($supports);
    }
}

class_alias(SectionCreateDenormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer\SectionCreateDenormalizerTest');
