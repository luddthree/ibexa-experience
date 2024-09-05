<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\FieldType\TaxonomyEntryAssignment;

use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Core\FieldType\ValidationError;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Taxonomy\Exception\TaxonomyEntryNotFoundException;
use Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment\Type;
use Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment\Value;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use PHPUnit\Framework\TestCase;

final class TypeTest extends TestCase
{
    private Type $type;

    /** @var \Ibexa\Taxonomy\Service\TaxonomyConfiguration|\PHPUnit\Framework\MockObject\MockObject */
    private TaxonomyConfiguration $taxonomyConfiguration;

    /** @var \Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private TaxonomyServiceInterface $taxonomyService;

    protected function setUp(): void
    {
        $this->taxonomyConfiguration = $this->createMock(TaxonomyConfiguration::class);
        $this->taxonomyService = $this->createMock(TaxonomyServiceInterface::class);

        $this->type = new Type(
            'ibexa_taxonomy_assignment_entry',
            $this->taxonomyService,
            $this->taxonomyConfiguration,
        );
    }

    public function testGetFieldTypeIdentifier(): void
    {
        self::assertEquals('ibexa_taxonomy_assignment_entry', $this->type->getFieldTypeIdentifier());
    }

    public function testGetName(): void
    {
        $fieldDefinition = $this->createMock(FieldDefinition::class);

        $taxonomyEntry = new TaxonomyEntry(
            1,
            'foobar',
            'Foobar',
            'eng-GB',
            [
                'eng-GB' => 'Foobar',
            ],
            null,
            new Content(),
            'tags',
        );

        self::assertEquals(
            'Foobar and 0 more',
            $this->type->getName(new Value([$taxonomyEntry]), $fieldDefinition, 'eng-GB'),
        );

        self::assertEmpty(
            $this->type->getName(new Value(), $fieldDefinition, 'eng-GB'),
        );
    }

    /**
     * @dataProvider dataProviderForTestFromHash
     *
     * @param array<string, int>|null $hash
     */
    public function testFromHash(?array $hash, Value $expectedValue): void
    {
        $this->taxonomyService
            ->method('loadEntryById')
            ->willReturn($this->createMock(TaxonomyEntry::class));

        self::assertEquals(
            $expectedValue,
            $this->type->fromHash($hash),
        );
    }

    /**
     * @dataProvider dataProviderForTestFromHash
     *
     * @param array<string, int>|null $hash
     */
    public function testFromHashWithEntriesNotFound(?array $hash, Value $expectedValue): void
    {
        $this->taxonomyService
            ->method('loadEntryById')
            ->willThrowException(new TaxonomyEntryNotFoundException());

        // Not found entries should be removed
        $expectedValue->setTaxonomyEntries([]);

        self::assertEquals(
            $expectedValue,
            $this->type->fromHash($hash),
        );
    }

    /**
     * @return iterable<array{
     *     array{taxonomy_entries: int[], taxonomy: string}|null,
     *     \Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment\Value
     * }>
     */
    public function dataProviderForTestFromHash(): iterable
    {
        yield 'null' => [
            null,
            new Value(),
        ];

        yield 'array' => [
            ['taxonomy_entries' => [1], 'taxonomy' => 'tags'],
            new Value(
                [$this->createMock(TaxonomyEntry::class)],
                'tags',
            ),
        ];
    }

    /**
     * @dataProvider dataProviderForTestToHash
     *
     * @param array<string, int>|null $expectedHash
     */
    public function testToHash(Value $value, ?array $expectedHash): void
    {
        self::assertEquals(
            $expectedHash,
            $this->type->toHash($value),
        );
    }

    /**
     * @return iterable<array{
     *     \Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment\Value,
     *     array{taxonomy_entries: int[], taxonomy: string|null},
     * }>
     */
    public function dataProviderForTestToHash(): iterable
    {
        yield 'empty value' => [
            new Value(),
            ['taxonomy_entries' => [], 'taxonomy' => null],
        ];

        $content = $this->createMock(Content::class);
        $entry = new TaxonomyEntry(
            1,
            'foo',
            'Foo',
            'eng-GB',
            [],
            null,
            $content,
            'tags',
        );

        yield 'array' => [
            new Value([
                $entry,
            ], 'tags'),
            ['taxonomy_entries' => [1], 'taxonomy' => 'tags'],
        ];
    }

    public function testIsEmptyValue(): void
    {
        self::assertTrue($this->type->isEmptyValue(new Value()));
        self::assertFalse($this->type->isEmptyValue(new Value([], 'tags')));
    }

    /**
     * @dataProvider dataProviderForTestValidateFieldSettings
     *
     * @param array<string, mixed> $fieldSettings
     * @param array<\Ibexa\Core\FieldType\ValidationError> $expectedErrors
     */
    public function testValidateFieldSettings(array $fieldSettings, array $expectedErrors): void
    {
        $this->taxonomyConfiguration
            ->method('getTaxonomies')
            ->willReturn(['foo', 'bar']);

        self::assertEquals($expectedErrors, $this->type->validateFieldSettings($fieldSettings));
    }

    /**
     * @return iterable<array{
     *     array<string, string>,
     *     array<\Ibexa\Core\FieldType\ValidationError>
     * }>
     */
    public function dataProviderForTestValidateFieldSettings(): iterable
    {
        $invalidSettingError = new ValidationError(
            "Setting '%setting%' is unknown",
            null,
            ['%setting%' => 'invalid_setting'],
            '[invalid_setting]'
        );
        $invalidTaxonomyError = new ValidationError(
            "Setting '%setting%' has invalid value. Allowed values are: %allowedValues%.",
            null,
            [
                '%setting%' => 'taxonomy',
                '%allowedValues%' => implode(', ', ['foo', 'bar']),
            ],
            '[taxonomy]'
        );

        yield 'invalid setting' => [
            ['invalid_setting' => 'foobar'],
            [$invalidSettingError],
        ];

        yield 'invalid taxonomy' => [
            ['taxonomy' => 'invalid_taxonomy_identifier'],
            [$invalidTaxonomyError],
        ];

        yield 'multiple errors' => [
            [
                'invalid_setting' => 'foobar',
                'taxonomy' => 'invalid_taxonomy_identifier',
            ],
            [
                $invalidSettingError,
                $invalidTaxonomyError,
            ],
        ];
    }

    public function testIsSearchable(): void
    {
        self::assertTrue($this->type->isSearchable());
    }
}
