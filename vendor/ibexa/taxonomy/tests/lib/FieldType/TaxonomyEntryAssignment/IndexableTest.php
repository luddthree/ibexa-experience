<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\FieldType\TaxonomyEntryAssignment;

use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Contracts\Core\Search;
use Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment\Indexable;
use Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntry;
use Ibexa\Taxonomy\Persistence\Repository\TaxonomyEntryRepository;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment\Indexable
 */
final class IndexableTest extends TestCase
{
    private const ENTRY_ID_LIST = [1, 2];

    private Indexable $indexable;

    /** @var \Ibexa\Taxonomy\Persistence\Repository\TaxonomyEntryRepository&\PHPUnit\Framework\MockObject\MockObject */
    private TaxonomyEntryRepository $taxonomyEntryRepositoryMock;

    public function setUp(): void
    {
        $this->taxonomyEntryRepositoryMock = $this->createMock(TaxonomyEntryRepository::class);
        $this->indexable = new Indexable($this->taxonomyEntryRepositoryMock);
    }

    /**
     * @param list<\Ibexa\Contracts\Core\Search\Field> $expectedSearchFieldList
     * @param list<\Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntry> $taxonomyEntityStubList
     *
     * @dataProvider getDataForTestGetIndexDataCreatesFullTextFields
     */
    public function testGetIndexDataCreatesFullTextFields(
        array $expectedSearchFieldList,
        array $taxonomyEntityStubList,
        Field $field
    ): void {
        $fieldDefinitionMock = $this->createMock(FieldDefinition::class);

        if (!empty($field->value->externalData['taxonomy_entries'])) {
            $this
                ->taxonomyEntryRepositoryMock
                ->expects(self::once())
                ->method('findBy')
                ->with(['id' => $field->value->externalData['taxonomy_entries']])
                ->willReturn($taxonomyEntityStubList)
            ;
        } else {
            $this->taxonomyEntryRepositoryMock->expects(self::never())->method('find');
        }

        self::assertEquals($expectedSearchFieldList, $this->indexable->getIndexData($field, $fieldDefinitionMock));
    }

    /**
     * @return iterable<
     *     string,
     *     array{
     *          array<\Ibexa\Contracts\Core\Search\Field>,
     *          array<\Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntry>,
     *          \Ibexa\Contracts\Core\Persistence\Content\Field
     *     }
     * >
     */
    public static function getDataForTestGetIndexDataCreatesFullTextFields(): iterable
    {
        $field = new Field();
        $field->languageCode = 'eng-GB';
        $field->value = new FieldValue();
        $field->value->externalData = [];

        $taxonomyEntryEntityStubList = [
            self::buildTaxonomyEntryEntityStub(['eng-GB' => 'en_tag_1', 'pol-PL' => 'pl_tag_1']),
            self::buildTaxonomyEntryEntityStub(['eng-GB' => 'en_tag_2', 'pol-PL' => 'pl_tag_2']),
        ];

        yield 'no entries' => [
            [],
            [],
            $field,
        ];

        $enField = clone $field;
        $enField->value->externalData = ['taxonomy_entries' => self::ENTRY_ID_LIST];
        yield 'two entries in English' => [
            self::buildExpectedSearchFieldListFromStrings('en_tag_1', 'en_tag_2'),
            $taxonomyEntryEntityStubList,
            $enField,
        ];

        $plField = clone $enField;
        $plField->languageCode = 'pol-PL';
        yield 'two entries in Polish' => [
            self::buildExpectedSearchFieldListFromStrings('pl_tag_1', 'pl_tag_2'),
            $taxonomyEntryEntityStubList,
            $plField,
        ];

        $deField = clone $plField;
        $deField->languageCode = 'ger-DE';
        yield 'missing translation entries' => [
            // for a TaxonomyEntry not translated to ger-DE it should fall back to a default name
            self::buildExpectedSearchFieldListFromStrings('en_tag_1', 'en_tag_2'),
            $taxonomyEntryEntityStubList,
            $deField,
        ];
    }

    /**
     * @param array<string, string> $names
     */
    private static function buildTaxonomyEntryEntityStub(array $names): TaxonomyEntry
    {
        self::assertNotEmpty($names);

        $taxonomyEntry = new TaxonomyEntry();
        $taxonomyEntry->setName(current($names));
        $taxonomyEntry->setNames($names);

        return $taxonomyEntry;
    }

    /**
     * @param array<string> $strings
     *
     * @return array<\Ibexa\Contracts\Core\Search\Field>
     */
    private static function buildExpectedSearchFieldListFromStrings(...$strings): array
    {
        $searchFields = [
            new Search\Field(
                'id',
                self::ENTRY_ID_LIST,
                new Search\FieldType\MultipleIntegerField()
            ),
            new Search\Field(
                'count',
                2, // checks sanity of expected count operation of self::ENTRY_ID_LIST
                new Search\FieldType\IntegerField()
            ),
            new Search\Field(
                'sort_value',
                '1-2', // see self::ENTRY_ID_LIST
                new Search\FieldType\StringField()
            ),
        ];

        $searchFields[] = new Search\Field(
            'fulltext',
            $strings,
            new Search\FieldType\FullTextField()
        );

        return $searchFields;
    }
}
