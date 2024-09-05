<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\Repository;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryUpdateStruct;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Taxonomy\Repository\Content\ContentSynchronizer;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use Ibexa\Tests\Taxonomy\ContentTestDataProvider;
use Ibexa\Tests\Taxonomy\FieldTestDataProvider;
use Ibexa\Tests\Taxonomy\TaxonomyEntryTestDataProvider;
use PHPUnit\Framework\TestCase;

final class ContentSynchronizerTest extends TestCase
{
    public function testSynchronize(): void
    {
        $parent = TaxonomyEntryTestDataProvider::getRootTaxonomyEntry();
        $contentType = ContentTestDataProvider::getSimpleContentType();
        $content = ContentTestDataProvider::getContent($contentType, FieldTestDataProvider::getFields($parent));

        $taxonomyConfiguration = $this->createMock(TaxonomyConfiguration::class);
        $taxonomyConfiguration
            ->method('getTaxonomyForContentType')
            ->willReturn('example_taxonomy');

        $taxonomyConfiguration
            ->expects($this->once())
            ->method('getFieldMappings')
            ->willReturn(
                ['parent' => 'parent_ft', 'identifier' => 'identifier_ft', 'name' => 'name_ft']
            );

        $taxonomyService = $this->createMock(TaxonomyServiceInterface::class);
        $taxonomyService
            ->expects(self::once())
            ->method('updateEntry')
            ->with(
                self::isInstanceOf(TaxonomyEntry::class),
                self::callback(function ($object): bool {
                    self::assertInstanceOf(TaxonomyEntryUpdateStruct::class, $object);

                    self::assertSame('updated_example_entry', $object->identifier);
                    self::assertSame('Updated example entry', $object->name);
                    self::assertSame(['eng-GB' => 'Updated example entry'], $object->names);
                    self::assertSame('eng-GB', $object->languageCode);
                    self::assertSame('eng-GB', $object->mainLanguageCode);
                    self::assertInstanceOf(Content::class, $object->content);
                    self::assertInstanceOf(TaxonomyEntry::class, $object->parent);

                    return true;
                })
            );

        $contentService = $this->createMock(ContentService::class);
        $contentService
            ->method('loadContent')
            ->willReturn($content);

        $contentSynchronizer = new ContentSynchronizer($taxonomyConfiguration, $taxonomyService, $contentService);

        $taxonomyEntry = TaxonomyEntryTestDataProvider::getTaxonomyEntry(
            null,
            ContentTestDataProvider::getContent($contentType, []),
        );

        $contentSynchronizer->synchronize($taxonomyEntry);
    }
}
