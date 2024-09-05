<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Seo\EventSubscriber\Migrations;

use Ibexa\Bundle\Seo\EventSubscriber\Migrations\FieldValueHashMapperSubscriber;
use Ibexa\Core\FieldType\Value as FieldTypeAPIValue;
use Ibexa\Migration\Event\FieldValueFromHashEvent;
use Ibexa\Migration\Event\HashFromFieldValueEvent;
use Ibexa\Seo\FieldType\SeoType;
use Ibexa\Seo\FieldType\SeoValue;
use Ibexa\Seo\Value\SeoTypesValue;
use Ibexa\Seo\Value\SeoTypeValue;
use PHPUnit\Framework\TestCase;

final class FieldValueHashMapperSubscriberTest extends TestCase
{
    private FieldValueHashMapperSubscriber $fieldValueHashMapperSubscriber;

    protected function setUp(): void
    {
        $this->fieldValueHashMapperSubscriber = new FieldValueHashMapperSubscriber();
    }

    /**
     * @return iterable<string, array{string, array<mixed>|null, array<mixed>|null}>
     */
    public function getDataForTestConvertScalarHashIntoObjects(): iterable
    {
        yield 'ibexa_seo field type hash' => [
            SeoType::IDENTIFIER,
            [],
            [],
        ];

        yield 'null field type hash' => [
            'other',
            null,
            null,
        ];

        $otherHash = ['some' => 'hash'];
        yield 'other field type hash' => [
            'other',
            $otherHash,
            $otherHash,
        ];
    }

    /**
     * @dataProvider getDataForTestConvertScalarHashIntoObjects
     *
     * @param array<mixed>|null $scalarHash
     * @param array<mixed>|null $expectedFieldValueHash
     */
    public function testConvertScalarHashIntoObjects(
        string $fieldTypeIdentifier,
        ?array $scalarHash,
        ?array $expectedFieldValueHash
    ): void {
        $event = new FieldValueFromHashEvent($fieldTypeIdentifier, [], $scalarHash);
        $this->fieldValueHashMapperSubscriber->convertScalarHashIntoObjects(
            $event
        );
        self::assertSame($expectedFieldValueHash, $event->getHash());
    }

    /**
     * @return iterable<string, array{\Ibexa\Core\FieldType\Value,  array<mixed>}>
     */
    public function getDataForTestConvertObjectHashIntoScalarHash(): iterable
    {
        yield 'SEO field type value' => [
            new SeoValue(
                new SeoTypesValue(
                    [
                        'foo' => new SeoTypeValue('foo', ['field1' => 'Field 1 content value']),
                        'bar' => new SeoTypeValue('bar', ['field2' => 'Field 2 content value']),
                    ]
                )
            ),
            [
                'types' => [
                    'foo' => [
                        'type' => 'foo',
                        'fields' => ['field1' => 'Field 1 content value'],
                    ],
                    'bar' => [
                        'type' => 'bar',
                        'fields' => ['field2' => 'Field 2 content value'],
                    ],
                ],
            ],
        ];

        yield 'Empty SEO field type value' => [
            (new SeoType())->getEmptyValue(),
            [
                'types' => [],
            ],
        ];

        $fieldValue = $this->createMock(FieldTypeAPIValue::class);
        yield 'Other field type value' => [
            $fieldValue,
            // expect no change
            ['value' => $fieldValue],
        ];
    }

    /**
     * @dataProvider getDataForTestConvertObjectHashIntoScalarHash
     *
     * @param array<mixed> $expectedHash
     */
    public function testConvertObjectHashIntoScalarHash(
        FieldTypeAPIValue $fieldValue,
        array $expectedHash
    ): void {
        $event = new HashFromFieldValueEvent($fieldValue, ['value' => $fieldValue]);
        $this->fieldValueHashMapperSubscriber->convertObjectHashIntoScalarHash(
            $event
        );
        self::assertSame($expectedHash, $event->getHash());
    }
}
