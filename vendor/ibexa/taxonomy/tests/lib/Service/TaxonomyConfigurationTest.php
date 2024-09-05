<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\Service;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Core\FieldType\TextLine\Value;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Taxonomy\Exception\TaxonomyConfigurationNotFoundException;
use Ibexa\Taxonomy\Exception\TaxonomyNotFoundException;
use Ibexa\Taxonomy\FieldType\TaxonomyEntry\Value as TaxonomyEntryValue;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use PHPUnit\Framework\TestCase;

final class TaxonomyConfigurationTest extends TestCase
{
    public const CONFIG = [
        'taxonomy_1' => [
            'parent_location_remote_id' => 'taxonomy_1_folder',
            'content_type' => 'taxonomy_1_ct',
            'field_mappings' => [
                'identifier' => 'field_identifier_1',
                'parent' => 'field_parent_1',
                'name' => 'field_name_1',
            ],
            'assigned_content_tab' => true,
        ],
        'taxonomy_2' => [
            'parent_location_remote_id' => 'taxonomy_2_folder',
            'content_type' => 'taxonomy_2_ct',
            'field_mappings' => [
                'identifier' => 'field_identifier_2',
                'parent' => 'field_parent_2',
                'name' => 'field_name_2',
            ],
            'assigned_content_tab' => false,
        ],
    ];

    public const TAXONOMIES = ['taxonomy_1', 'taxonomy_2'];

    protected TaxonomyConfiguration $taxonomyConfiguration;

    protected function setUp(): void
    {
        $this->taxonomyConfiguration = new TaxonomyConfiguration(self::CONFIG);
    }

    /**
     * @dataProvider dataProviderForTestGetFieldMappings
     *
     * @param array<string, string> $expectedFieldMappings
     */
    public function testGetFieldMappings(string $taxonomyName, array $expectedFieldMappings): void
    {
        self::assertEquals(
            $expectedFieldMappings,
            $this->taxonomyConfiguration->getFieldMappings($taxonomyName)
        );
    }

    /**
     * @return iterable<array{
     *      string,
     *      array<string, string>
     * }>
     */
    public function dataProviderForTestGetFieldMappings(): iterable
    {
        yield [
            'taxonomy_1',
            [
                'identifier' => 'field_identifier_1',
                'parent' => 'field_parent_1',
                'name' => 'field_name_1',
            ],
            'assigned_content_tab' => true,
        ];

        yield [
            'taxonomy_2',
            [
                'identifier' => 'field_identifier_2',
                'parent' => 'field_parent_2',
                'name' => 'field_name_2',
            ],
            'assigned_content_tab' => false,
        ];
    }

    public function testGetFieldMappingsThrowOnInvalidTaxonomyName(): void
    {
        $this->expectException(TaxonomyNotFoundException::class);

        $this->taxonomyConfiguration->getFieldMappings('invalid_taxonomy_name');
    }

    /**
     * @dataProvider dataProviderForTestGetTaxonomyForContentType
     */
    public function testGetTaxonomyForContentType(ContentType $contentType, string $expectedTaxonomy): void
    {
        self::assertEquals(
            $expectedTaxonomy,
            $this->taxonomyConfiguration->getTaxonomyForContentType($contentType)
        );
    }

    /**
     * @return iterable<array{
     *      \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType,
     *      string,
     * }>
     */
    public function dataProviderForTestGetTaxonomyForContentType(): iterable
    {
        yield [
            new ContentType(['identifier' => 'taxonomy_1_ct']),
            'taxonomy_1',
        ];

        yield [
            new ContentType(['identifier' => 'taxonomy_2_ct']),
            'taxonomy_2',
        ];
    }

    public function testGetTaxonomyForContentTypeThrowWhenContentTypeDoesNotMatchTaxonomy(): void
    {
        $this->expectException(TaxonomyNotFoundException::class);
        $this->expectExceptionMessage('Content type Invalid CT (invalid_ct) is not associated with any taxonomy');

        $contentType = new ContentType([
            'names' => [
                'eng-GB' => 'Invalid CT',
            ],
            'identifier' => 'invalid_ct',
        ]);

        $this->taxonomyConfiguration->getTaxonomyForContentType($contentType);
    }

    /**
     * @dataProvider dataProviderForTestGetConfigForTaxonomy
     *
     * @param mixed $expectedValue
     */
    public function testGetConfigForTaxonomy(string $taxonomy, string $configName, $expectedValue): void
    {
        self::assertEquals(
            $expectedValue,
            $this->taxonomyConfiguration->getConfigForTaxonomy($taxonomy, $configName)
        );
    }

    /**
     * @return iterable<array{
     *      string,
     *      string,
     *      mixed,
     * }>
     */
    public function dataProviderForTestGetConfigForTaxonomy(): iterable
    {
        yield [
            'taxonomy_1',
            'parent_location_remote_id',
            'taxonomy_1_folder',
        ];

        yield [
            'taxonomy_1',
            'field_mappings',
            [
                'identifier' => 'field_identifier_1',
                'parent' => 'field_parent_1',
                'name' => 'field_name_1',
            ],
        ];

        yield [
            'taxonomy_2',
            'content_type',
            'taxonomy_2_ct',
        ];
    }

    public function testGetConfigForTaxonomyThrowOnInvalidTaxonomy(): void
    {
        $this->expectException(TaxonomyNotFoundException::class);
        $this->expectExceptionMessage("Taxonomy 'invalid_taxonomy' not found.");

        $this->taxonomyConfiguration->getConfigForTaxonomy('invalid_taxonomy', 'parent_location_remote_id');
    }

    public function testGetConfigForTaxonomyThrowOnMissingConfiguration(): void
    {
        $this->expectException(TaxonomyConfigurationNotFoundException::class);
        $this->expectExceptionMessage("Configuration 'invalid_configuration' of taxonomy 'taxonomy_1' not found.");

        $this->taxonomyConfiguration->getConfigForTaxonomy('taxonomy_1', 'invalid_configuration');
    }

    public function testGetTaxonomies(): void
    {
        self::assertEquals(
            self::TAXONOMIES,
            $this->taxonomyConfiguration->getTaxonomies()
        );
    }

    public function testGetEntryIdentifierFieldFromContent(): void
    {
        $content = $this->createContentWithFields();

        self::assertEquals(
            'foo',
            $this->taxonomyConfiguration->getEntryIdentifierFieldFromContent($content),
        );
    }

    /**
     * @dataProvider dataProviderForTestGetEntryParentFieldFromContent
     */
    public function testGetEntryParentFieldFromContent(Content $content, ?TaxonomyEntry $expectedEntry): void
    {
        self::assertEquals(
            $expectedEntry,
            $this->taxonomyConfiguration->getEntryParentFieldFromContent($content)
        );
    }

    /**
     * @return iterable<array{
     *      \Ibexa\Contracts\Core\Repository\Values\Content\Content,
     *      \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry|null,
     * }>
     */
    public function dataProviderForTestGetEntryParentFieldFromContent(): iterable
    {
        $content = $this->createContentWithFields();

        yield [
            $content,
            new TaxonomyEntry(
                1,
                'foo',
                'Foo',
                'eng-GB',
                [],
                null,
                new Content(),
                'taxonomy_1'
            ),
        ];

        $contentWithNoParentEntry = new Content([
            'contentType' => new ContentType(['identifier' => 'taxonomy_1_ct']),
            'versionInfo' => new VersionInfo([
                'contentInfo' => new ContentInfo([
                    'mainLanguageCode' => 'eng-GB',
                ]),
            ]),
        ]);

        yield [
            $contentWithNoParentEntry,
            null,
        ];
    }

    /**
     * @dataProvider dataProviderForTestIsContentTypeAssociatedWithTaxonomy
     */
    public function testIsContentTypeAssociatedWithTaxonomy(ContentType $contentType, bool $expectedResult): void
    {
        self::assertEquals(
            $expectedResult,
            $this->taxonomyConfiguration->isContentTypeAssociatedWithTaxonomy($contentType)
        );
    }

    /**
     * @return iterable<array{
     *      \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType,
     *      bool,
     * }>
     */
    public function dataProviderForTestIsContentTypeAssociatedWithTaxonomy(): iterable
    {
        yield [
            new ContentType([
                'identifier' => 'taxonomy_1_ct',
            ]),
            true,
        ];

        yield [
            new ContentType([
                'identifier' => 'ct_not_associated_with_taxonomy',
            ]),
            false,
        ];
    }

    private function createContentWithFields(): Content
    {
        return new Content([
            'contentType' => new ContentType(['identifier' => 'taxonomy_1_ct']),
            'versionInfo' => new VersionInfo([
                'contentInfo' => new ContentInfo([
                    'mainLanguageCode' => 'eng-GB',
                ]),
            ]),
            'internalFields' => [
                new Field([
                    'fieldDefIdentifier' => 'field_identifier_1',
                    'fieldTypeIdentifier' => 'ibexa_string',
                    'value' => new Value('foo'),
                    'languageCode' => 'eng-GB',
                ]),
                new Field([
                    'fieldDefIdentifier' => 'field_parent_1',
                    'fieldTypeIdentifier' => 'ibexa_taxonomy_entry',
                    'value' => new TaxonomyEntryValue(
                        new TaxonomyEntry(
                            1,
                            'foo',
                            'Foo',
                            'eng-GB',
                            [],
                            null,
                            new Content(),
                            'taxonomy_1'
                        ),
                    ),
                    'languageCode' => 'eng-GB',
                ]),
                new Field([
                    'fieldDefIdentifier' => 'field_name_1',
                    'fieldTypeIdentifier' => 'ibexa_string',
                    'value' => new Value('Foo'),
                    'languageCode' => 'eng-GB',
                ]),
            ],
        ]);
    }
}
