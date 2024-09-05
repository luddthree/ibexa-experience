<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\Extractor;

use Ibexa\ContentForms\Data\Content\ContentUpdateData;
use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Taxonomy\Extractor\TaxonomyEntryExtractor;
use Ibexa\Taxonomy\FieldType\TaxonomyEntry\Value;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use PHPUnit\Framework\TestCase;

final class TaxonomyEntryExtractorTest extends TestCase
{
    /** @var \Ibexa\Taxonomy\Service\TaxonomyConfiguration|\PHPUnit\Framework\MockObject\MockObject */
    private TaxonomyConfiguration $taxonomyConfiguration;

    private TaxonomyEntryExtractor $extractor;

    protected function setUp(): void
    {
        $this->taxonomyConfiguration = $this->createMock(TaxonomyConfiguration::class);
        $this->extractor = new TaxonomyEntryExtractor($this->taxonomyConfiguration);
    }

    /**
     * @dataProvider dataProviderForTestExtractEntryParentFromContentUpdateData
     */
    public function testExtractEntryParentFromContentUpdateData(
        ContentUpdateData $contentUpdateData,
        ?ContentInfo $expectedContentInfo
    ): void {
        $this->taxonomyConfiguration
            ->method('getTaxonomyForContentType')
            ->willReturn('foobar');
        $this->taxonomyConfiguration
            ->method('getFieldMappings')
            ->with('foobar')
            ->willReturn([
                'identifier' => 'field_identifier',
                'name' => 'field_name',
                'parent' => 'field_parent',
            ]);

        self::assertEquals(
            $expectedContentInfo,
            $this->extractor->extractEntryParentFromContentUpdateData($contentUpdateData)
        );
    }

    /**
     * @return iterable<array{\Ibexa\ContentForms\Data\Content\ContentUpdateData, \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo|null}>
     */
    public function dataProviderForTestExtractEntryParentFromContentUpdateData(): iterable
    {
        $taxonomyEntry = new TaxonomyEntry(
            1,
            'foo',
            'Foo',
            'eng-GB',
            [],
            null,
            new Content([
                'versionInfo' => new VersionInfo([
                    'contentInfo' => new ContentInfo(['id' => 5]),
                ]),
            ]),
            'foobar'
        );

        yield 'null' => [
            new ContentUpdateData([
                'contentDraft' => new Content([
                    'contentType' => new ContentType(),
                ]),
                'fieldsData' => [
                    'field_parent' => new FieldData([
                        'value' => new Value(),
                    ]),
                ],
            ]),
            null,
        ];

        yield 'taxonomy entry' => [
            new ContentUpdateData([
                'contentDraft' => new Content([
                    'contentType' => new ContentType(),
                ]),
                'fieldsData' => [
                    'field_parent' => new FieldData(['value' => new Value($taxonomyEntry)]),
                ],
            ]),
            new ContentInfo(['id' => 5]),
        ];
    }
}
