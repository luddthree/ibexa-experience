<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\LanguageCreateDenormalizer;
use Ibexa\Migration\ValueObject\Step\LanguageCreateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Tests\Bundle\Migration\IbexaKernelTestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class LanguageCreateDenormalizerTest extends IbexaKernelTestCase
{
    /** @var \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\LanguageCreateDenormalizer */
    private $denormalizer;

    protected function setUp(): void
    {
        $this->denormalizer = new LanguageCreateDenormalizer();
        $this->denormalizer->setDenormalizer($this->createMock(DenormalizerInterface::class));
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
            'type' => 'language',
            'mode' => 'create',
            'name' => 'Polish',
            'lang' => 'pol-PL',
        ];

        $expectedResult = [
            'type' => 'language',
            'mode' => 'create',
            'metadata' => [
                'languageCode' => 'pol-PL',
                'name' => 'Polish',
                'enabled' => true,
            ],
        ];

        yield [$data, $expectedResult];
    }

    public function testSupportsDenormalization(): void
    {
        $supports = $this->denormalizer->supportsDenormalization(null, StepInterface::class);
        self::assertFalse($supports);

        $supports = $this->denormalizer->supportsDenormalization([
            'type' => 'language',
            'mode' => 'create',
        ], LanguageCreateStep::class);
        self::assertFalse($supports);

        $supports = $this->denormalizer->supportsDenormalization([
            'type' => 'language',
            'mode' => 'create',
        ], StepInterface::class);
        self::assertTrue($supports);

        $supports = $this->denormalizer->supportsDenormalization(null, '');
        self::assertFalse($supports);
    }
}

class_alias(LanguageCreateDenormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer\LanguageCreateDenormalizerTest');
