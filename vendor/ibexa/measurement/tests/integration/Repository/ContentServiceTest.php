<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Measurement\Repository;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Measurement\MeasurementServiceInterface;
use Ibexa\Contracts\Measurement\Value\RangeValueInterface;
use Ibexa\Contracts\Measurement\Value\SimpleValueInterface;
use Ibexa\Measurement\FieldType\MeasurementValue;

final class ContentServiceTest extends BaseIntegrationTestCase
{
    private const LENGTH_TYPE = 'length';
    private const LENGTH_CM = 'centimeter';
    private const LENGTH_VALUE = 2.5;
    private const LENGTH_VALUE_MIN = 1.2;
    private const LENGTH_VALUE_MAX = 3.1;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testPublishContentWithMeasurementValue(): void
    {
        $contentService = self::getContentService();
        $measurementService = self::getServiceByClassName(MeasurementServiceInterface::class);
        $content = $this->publishContentWithMeasurementValue(
            $contentService,
            new MeasurementValue(
                $measurementService->buildSimpleValue(
                    self::LENGTH_TYPE,
                    self::LENGTH_VALUE,
                    self::LENGTH_CM
                )
            )
        );

        $content = $contentService->loadContent($content->id);
        $field = $content->getField('measurement');
        self::assertNotNull($field);
        $measurementFieldValue = $field->value;
        self::assertInstanceOf(MeasurementValue::class, $measurementFieldValue);
        $measurementValue = $measurementFieldValue->getValue();
        self::assertInstanceOf(SimpleValueInterface::class, $measurementValue);
        self::assertSame(self::LENGTH_TYPE, $measurementValue->getMeasurement()->getName());
        self::assertSame(self::LENGTH_CM, $measurementValue->getUnit()->getIdentifier());
        self::assertSame(self::LENGTH_VALUE, $measurementValue->getValue());
    }

    public function testPublishContentWithInvalidMeasurementType(): void
    {
        $contentService = self::getContentService();
        $measurementService = self::getServiceByClassName(MeasurementServiceInterface::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'$type\' is invalid: foo does not exist');

        $this->publishContentWithMeasurementValue(
            $contentService,
            new MeasurementValue(
                $measurementService->buildSimpleValue(
                    'foo',
                    self::LENGTH_VALUE,
                    self::LENGTH_CM
                )
            )
        );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testPublishContentWithMeasurementRange(): void
    {
        $contentService = self::getContentService();
        $measurementService = self::getServiceByClassName(MeasurementServiceInterface::class);
        $content = $this->publishContentWithMeasurementValue(
            $contentService,
            new MeasurementValue(
                $measurementService->buildRangeValue(
                    self::LENGTH_TYPE,
                    self::LENGTH_VALUE_MIN,
                    self::LENGTH_VALUE_MAX,
                    self::LENGTH_CM
                )
            )
        );

        $content = $contentService->loadContent($content->id);
        $field = $content->getField('measurement');
        self::assertNotNull($field);
        $measurementFieldValue = $field->value;
        self::assertInstanceOf(MeasurementValue::class, $measurementFieldValue);
        $nativeValue = $measurementFieldValue->getValue();
        self::assertInstanceOf(RangeValueInterface::class, $nativeValue);
        self::assertSame(self::LENGTH_TYPE, $nativeValue->getMeasurement()->getName());
        self::assertSame(self::LENGTH_CM, $nativeValue->getUnit()->getIdentifier());
        self::assertSame(self::LENGTH_VALUE_MIN, $nativeValue->getMinValue());
        self::assertSame(self::LENGTH_VALUE_MAX, $nativeValue->getMaxValue());
    }

    private function publishContentWithMeasurementValue(
        ContentService $contentService,
        MeasurementValue $measurementFieldValue
    ): Content {
        $measurementContentType = $this->createMeasurementContentType();

        $contentCreate = $contentService->newContentCreateStruct($measurementContentType, 'eng-US');
        $contentCreate->setField('name', 'Test');
        $contentCreate->setField('measurement', $measurementFieldValue);
        $contentDraft = $contentService->createContent($contentCreate);

        return $contentService->publishVersion($contentDraft->versionInfo);
    }
}
