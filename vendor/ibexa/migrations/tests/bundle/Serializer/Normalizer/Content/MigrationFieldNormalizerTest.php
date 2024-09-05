<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Normalizer\Content;

use Ibexa\Bundle\Migration\Serializer\Normalizer\Content\MigrationFieldNormalizer;
use Ibexa\Migration\ValueObject\Content\Field;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Normalizer\Content\MigrationFieldNormalizer
 */
final class MigrationFieldNormalizerTest extends TestCase
{
    /** @var \Ibexa\Bundle\Migration\Serializer\Normalizer\Content\MigrationFieldNormalizer */
    private $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new MigrationFieldNormalizer();
        $mockedNormalizer = $this->createMock(NormalizerInterface::class);
        $mockedNormalizer->method('normalize')->willReturnArgument(0);
        $this->normalizer->setNormalizer($mockedNormalizer);
    }

    public function testNormalize(): void
    {
        self::assertFalse($this->normalizer->supportsNormalization(null));

        $field = Field::createFromArray([
            'fieldDefIdentifier' => '__field_def_identifier__',
            'value' => '__value__',
        ]);
        self::assertTrue($this->normalizer->supportsNormalization($field));
        self::assertEquals([
            'fieldDefIdentifier' => '__field_def_identifier__',
            'languageCode' => null,
            'value' => '__value__',
        ], $this->normalizer->normalize($field));

        $field = Field::createFromArray([
            'fieldDefIdentifier' => '__field_def_identifier__',
            'value' => '__value__',
            'languageCode' => '__language_code__',
        ]);
        self::assertTrue($this->normalizer->supportsNormalization($field));
        self::assertEquals([
            'fieldDefIdentifier' => '__field_def_identifier__',
            'languageCode' => '__language_code__',
            'value' => '__value__',
        ], $this->normalizer->normalize($field));
    }

    /**
     * @dataProvider providerField
     *
     * @param array<string, mixed> $fieldData
     * @param array<string, mixed> $context
     */
    public function testDenormalize(array $fieldData, array $context, Field $expectedField): void
    {
        self::assertTrue($this->normalizer->supportsDenormalization(null, Field::class));
        $denormalized = $this->normalizer->denormalize($fieldData, Field::class, null, $context);

        self::assertInstanceOf(Field::class, $denormalized);
        self::assertEquals($expectedField, $denormalized);
    }

    /**
     * @return iterable<string, array{array<string, mixed>, array, \Ibexa\Migration\ValueObject\Content\Field}>
     */
    public function providerField(): iterable
    {
        $fieldData = [
            'fieldDefIdentifier' => '__field_def_identifier__',
            'languageCode' => '__language_code__',
            'value' => '__value__',
        ];

        $expected = Field::createFromArray([
            'fieldDefIdentifier' => '__field_def_identifier__',
            'value' => '__value__',
            'languageCode' => '__language_code__',
        ]);

        yield 'with given languageCode' => [
            $fieldData,
            [],
            $expected,
        ];

        $fieldData = [
            'fieldDefIdentifier' => '__field_def_identifier__',
            'languageCode' => null,
            'value' => '__value__',
        ];

        $expected = Field::createFromArray([
            'fieldDefIdentifier' => '__field_def_identifier__',
            'value' => '__value__',
            'languageCode' => null,
        ]);

        yield 'without languageCode' => [
            $fieldData,
            [],
            $expected,
        ];

        $fieldData = [
            'fieldDefIdentifier' => '__field_def_identifier__',
            'languageCode' => null,
            'value' => '__value__',
        ];

        $context = [
            'mainLanguage' => '__language_code__',
        ];

        $expected = Field::createFromArray([
            'fieldDefIdentifier' => '__field_def_identifier__',
            'value' => '__value__',
            'languageCode' => '__language_code__',
        ]);

        yield 'without languageCode but with mainLanguage in context' => [
            $fieldData,
            $context,
            $expected,
        ];
    }
}

class_alias(MigrationFieldNormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Normalizer\Content\MigrationFieldNormalizerTest');
