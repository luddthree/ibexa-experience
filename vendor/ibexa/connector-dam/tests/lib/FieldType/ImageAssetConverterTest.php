<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Connector\Dam\FieldType;

use Ibexa\Connector\Dam\Persistance\FieldValue\ImageAssetConverter;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldValue;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Connector\Dam\Persistance\FieldValue\ImageAssetConverter
 */
final class ImageAssetConverterTest extends TestCase
{
    /** @var \Ibexa\Connector\Dam\Persistance\FieldValue\ImageAssetConverter */
    protected $converter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->converter = new ImageAssetConverter();
    }

    /**
     * @phpstan-return iterable<array{
     *     \Ibexa\Contracts\Core\Persistence\Content\FieldValue,
     *     \Ibexa\Core\Persistence\Legacy\Content\StorageFieldValue,
     * }>
     */
    public function provideDataForToStorageValue(): iterable
    {
        yield [
            new FieldValue(['data' => null]),
            new StorageFieldValue(['dataText' => null]),
        ];

        yield [
            new FieldValue(['data' => [
                'destinationContentId' => 42,
                'alternativeText' => 'some text',
            ]]),
            new StorageFieldValue([
                'dataText' => '{"destinationContentId":42,"alternativeText":"some text"}',
            ]),
        ];
    }

    /**
     * @dataProvider provideDataForToStorageValue
     */
    public function testToStorageValue(FieldValue $value, StorageFieldValue $expected): void
    {
        $storageFieldValue = new StorageFieldValue();
        $this->converter->toStorageValue($value, $storageFieldValue);

        self::assertEquals($expected, $storageFieldValue);
    }

    /**
     * @phpstan-return iterable<array{
     *     \Ibexa\Core\Persistence\Legacy\Content\StorageFieldValue,
     *     \Ibexa\Contracts\Core\Persistence\Content\FieldValue,
     * }>
     */
    public function provideDataForToFieldValue(): iterable
    {
        yield [
            new StorageFieldValue(['dataText' => null]),
            new FieldValue(['data' => [
                'destinationContentId' => null,
                'alternativeText' => null,
                'source' => null,
            ]]),
        ];

        yield [
            new StorageFieldValue([
                'dataText' => '{"destinationContentId":42,"alternativeText":"some text"}',
            ]),
            new FieldValue(['data' => [
                'destinationContentId' => 42,
                'alternativeText' => 'some text',
            ]]),
        ];

        yield [
            new StorageFieldValue([
                'dataText' => '{"destinationContentId":42,"alternativeText":"some text","source":"some source"}',
            ]),
            new FieldValue(['data' => [
                'destinationContentId' => 42,
                'alternativeText' => 'some text',
                'source' => 'some source',
            ]]),
        ];
    }

    /**
     * @dataProvider provideDataForToFieldValue
     */
    public function testToFieldValue(StorageFieldValue $value, FieldValue $expected): void
    {
        $fieldValue = new FieldValue();
        $this->converter->toFieldValue($value, $fieldValue);

        self::assertEquals($expected, $fieldValue);
    }
}

class_alias(ImageAssetConverterTest::class, 'Ibexa\Platform\Tests\Connector\Dam\FieldType\ImageAssetConverterTest');
