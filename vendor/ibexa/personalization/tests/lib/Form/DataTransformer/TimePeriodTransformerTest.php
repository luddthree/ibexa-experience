<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Form\DataTransformer;

use Ibexa\Personalization\Form\Data\TimePeriodData;
use Ibexa\Personalization\Form\DataTransformer\TimePeriodTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @covers \Ibexa\Personalization\Form\DataTransformer\TimePeriodTransformer
 */
final class TimePeriodTransformerTest extends TestCase
{
    private TimePeriodTransformer $timePeriodTransformer;

    protected function setUp(): void
    {
        $this->timePeriodTransformer = new TimePeriodTransformer();
    }

    public function testTransformThrowTransformationFailedException(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Invalid data. Value should be of type: %s, array given.',
                TimePeriodData::class,
            )
        );

        /** @phpstan-ignore-next-line */
        $this->timePeriodTransformer->transform([]);
    }

    public function testReverseTransformThrowTransformationFailedExceptionNegativeInteger(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Invalid data. Value should be positive integer, "-1" given.');

        $this->timePeriodTransformer->reverseTransform(new TimePeriodData('-1', 'D'));
    }

    public function testReverseTransformThrowTransformationFailedExceptionIncompatibleIsoSpecification(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage(
            'Invalid data. Value should be compatible with ISO 8601 duration specification. "-1ABC" given.'
        );

        $this->timePeriodTransformer->reverseTransform(new TimePeriodData('-1ABC'));
    }

    /**
     * @dataProvider provideDataForTestTransform
     */
    public function testTransform(
        TimePeriodData $expectedTimePeriod,
        TimePeriodData $actualTimePeriod
    ): void {
        self::assertEquals(
            $expectedTimePeriod,
            $this->timePeriodTransformer->transform($actualTimePeriod)
        );
    }

    /**
     * @dataProvider provideDataForTestReverseTransform
     */
    public function testReverseTransform(
        TimePeriodData $expectedTimePeriod,
        TimePeriodData $actualTimePeriod
    ): void {
        self::assertEquals(
            $expectedTimePeriod,
            $this->timePeriodTransformer->reverseTransform($actualTimePeriod)
        );
    }

    /**
     * @return iterable<array{
     *     \Ibexa\Personalization\Form\Data\TimePeriodData,
     *     \Ibexa\Personalization\Form\Data\TimePeriodData
     * }>
     */
    public function provideDataForTestTransform(): iterable
    {
        yield [
            new TimePeriodData(),
            new TimePeriodData(),
        ];

        yield [
            new TimePeriodData('1', 'D'),
            new TimePeriodData('P1D'),
        ];

        yield [
            new TimePeriodData('24', 'H'),
            new TimePeriodData('PT24H'),
        ];
    }

    /**
     * @return iterable<array{
     *     \Ibexa\Personalization\Form\Data\TimePeriodData,
     *     \Ibexa\Personalization\Form\Data\TimePeriodData
     * }>
     */
    public function provideDataForTestReverseTransform(): iterable
    {
        yield [
            new TimePeriodData(),
            new TimePeriodData(),
        ];

        yield [
            new TimePeriodData('P1D'),
            new TimePeriodData('1', 'D'),
        ];

        yield [
            new TimePeriodData('PT24H'),
            new TimePeriodData('24', 'H'),
        ];
    }
}
