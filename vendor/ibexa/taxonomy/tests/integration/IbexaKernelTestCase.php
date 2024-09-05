<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Taxonomy;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Test\IbexaKernelTestCase as BaseIbexaKernelTestCase;
use Ibexa\Contracts\Taxonomy\Search\Query\Criterion\TaxonomySubtree;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Core\FieldType\TextLine\Value as TextValue;
use Ibexa\Taxonomy\Exception\TaxonomyEntryNotFoundException;
use Ibexa\Taxonomy\FieldType\TaxonomyEntry\Value as TaxonomyEntryValue;
use Ibexa\Taxonomy\Persistence\Repository\TaxonomyEntryAssignmentRepository;
use Ibexa\Taxonomy\Persistence\Repository\TaxonomyEntryRepository;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use Ibexa\Taxonomy\Tree\TaxonomyTreeServiceInterface;
use Ibexa\Tests\Integration\Taxonomy\Constraint\TaxonomyTagCount;

abstract class IbexaKernelTestCase extends BaseIbexaKernelTestCase
{
    protected static function getTaxonomyService(): TaxonomyServiceInterface
    {
        return self::getServiceByClassName(TaxonomyServiceInterface::class);
    }

    protected static function getTaxonomyTreeService(): TaxonomyTreeServiceInterface
    {
        return self::getServiceByClassName(TaxonomyTreeServiceInterface::class);
    }

    protected static function getTaxonomyConfiguration(): TaxonomyConfiguration
    {
        return self::getServiceByClassName(TaxonomyConfiguration::class);
    }

    protected static function getTaxonomyEntryRepository(): TaxonomyEntryRepository
    {
        return self::getServiceByClassName(TaxonomyEntryRepository::class);
    }

    protected static function getTaxonomyEntryAssignmentRepository(): TaxonomyEntryAssignmentRepository
    {
        return self::getServiceByClassName(TaxonomyEntryAssignmentRepository::class);
    }

    protected static function assertContentTypeExists(string $identifier): void
    {
        try {
            $contentTypeService = self::getContentTypeService();
            $contentTypeService->loadContentTypeByIdentifier($identifier);
            $found = true;
        } catch (NotFoundException $e) {
            $found = false;
        }

        self::assertTrue($found, sprintf('ContentType with identifier "%s" does not exist.', $identifier));
    }

    protected static function assertTags(
        int $id,
        int $expectedCount,
        ?string $taxonomyIdentifier = null,
        string $message = ''
    ): void {
        self::assertThat(
            $id,
            new TaxonomyTagCount($expectedCount, self::getSearchService(), $taxonomyIdentifier),
            $message,
        );
    }

    protected static function assertSubtreeTags(int $id, int $expectedCount, string $message = ''): void
    {
        $searchResult = self::getSearchService()->findContent(new Query([
            'filter' => new TaxonomySubtree($id),
        ]));

        self::assertCount($expectedCount, $searchResult, $message);
    }

    protected static function assertTaxonomyEntryDoesNotExist(string $identifier, string $taxonomy, string $message = ''): void
    {
        try {
            self::getTaxonomyService()->loadEntryByIdentifier($identifier, $taxonomy);
            $found = true;
        } catch (TaxonomyEntryNotFoundException $e) {
            $found = false;
        }

        self::assertFalse($found, sprintf("Entry '%s' in taxonomy '%s' exists.", $identifier, $taxonomy));
    }

    protected static function createTag(?TaxonomyEntry $parent, string $identifier, string $contentTypeForTag = 'tag'): TaxonomyEntry
    {
        $contentService = self::getContentService();
        $tagContentType = self::getContentTypeService()->loadContentTypeByIdentifier($contentTypeForTag);
        $struct = $contentService->newContentCreateStruct($tagContentType, 'eng-GB');
        if ($parent !== null) {
            $struct->setField('parent', new TaxonomyEntryValue($parent));
        }
        $struct->setField('identifier', new TextValue($identifier));
        $struct->setField('name', ucfirst($identifier));
        $content = $contentService->createContent($struct);
        $contentService->publishVersion($content->getVersionInfo());

        $taxonomy = self::getTaxonomyConfiguration()->getTaxonomyForContentType($tagContentType);

        return self::getTaxonomyService()->loadEntryByIdentifier($identifier, $taxonomy);
    }
}
