<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Form\DataTransformer;

use DateTimeImmutable;
use Ibexa\Personalization\Factory\DateTime\GranularityFactoryInterface;
use Ibexa\Personalization\Form\Data\DateIntervalData;
use Ibexa\Personalization\Form\Data\DateTimeRangeData;
use Ibexa\Personalization\Form\DataTransformer\DateIntervalTransformer;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class DateIntervalTransformerTest extends TestCase
{
    private const GRANULARITY_1H = 'PT1H';
    private const GRANULARITY_1D = 'P1D';

    /** @var \Ibexa\Tests\Personalization\Form\DataTransformer\DateIntervalTransformer */
    private $dateIntervalTransformer;

    /** @var \Ibexa\Personalization\Factory\DateTime\GranularityFactoryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $granularityFactory;

    /** @var \DateTimeImmutable */
    private $startDate;

    /** @var \DateTimeImmutable */
    private $endDate;

    public function setUp(): void
    {
        $this->granularityFactory = $this->createMock(GranularityFactoryInterface::class);
        $this->dateIntervalTransformer = new DateIntervalTransformer($this->granularityFactory);
        $this->startDate = (new DateTimeImmutable())->setTime(00, 00, 00);
        $this->endDate = (new DateTimeImmutable())->setTime(00, 00, 00);
    }

    public function testCreateInstanceDataTransformer(): void
    {
        self::assertInstanceOf(
            DataTransformerInterface::class,
            $this->dateIntervalTransformer
        );
    }

    /**
     * @dataProvider providerForTestThrowTransformationFailedExceptionWhenValueIsNotDateIntervalData
     */
    public function testThrowTransformationFailedExceptionWhenValueIsNotDateIntervalData(
        $value,
        string $message
    ): void {
        self::expectException(TransformationFailedException::class);
        self::expectExceptionMessage($message);

        $this->dateIntervalTransformer->reverseTransform($value);
    }

    public function providerForTestThrowTransformationFailedExceptionWhenValueIsNotDateIntervalData(): iterable
    {
        $message = 'Invalid data. Value should be type of: ' . DateIntervalData::class . '. %s given';

        yield [
            12345,
            sprintf($message, 'integer'),
        ];
        yield [
            'value',
            sprintf($message, 'string'),
        ];
        yield [
            [],
            sprintf($message, 'array'),
        ];
        yield [
            new stdClass(),
            sprintf($message, 'stdClass'),
        ];
        yield [
            new DateTimeRangeData(),
            sprintf($message, DateTimeRangeData::class),
        ];
    }
}

class_alias(DateIntervalTransformerTest::class, 'Ibexa\Platform\Tests\Personalization\Form\DataTransformer\DateIntervalTransformerTest');
