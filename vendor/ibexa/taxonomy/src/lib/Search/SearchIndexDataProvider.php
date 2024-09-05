<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Search;

use Ibexa\Contracts\Core\Persistence\Content as SPIContent;
use Ibexa\Contracts\Core\Search;
use Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntry;
use Ibexa\Taxonomy\Persistence\Repository\TaxonomyEntryAssignmentRepository;
use Ibexa\Taxonomy\Persistence\Repository\TaxonomyEntryRepository;

/**
 * @internal
 */
final class SearchIndexDataProvider
{
    private TaxonomyEntryAssignmentRepository $taxonomyEntryAssignmentRepository;

    private TaxonomyEntryRepository $entryRepository;

    public function __construct(
        TaxonomyEntryAssignmentRepository $taxonomyEntryAssignmentRepository,
        TaxonomyEntryRepository $entryRepository
    ) {
        $this->taxonomyEntryAssignmentRepository = $taxonomyEntryAssignmentRepository;

        $this->entryRepository = $entryRepository;
    }

    /**
     * @return \Ibexa\Contracts\Core\Search\Field[]
     */
    public function getSearchData(SPIContent $content): array
    {
        $entryAssignments = $this->taxonomyEntryAssignmentRepository->findBy([
            'content' => $content->versionInfo->contentInfo->id,
            'versionNo' => $content->versionInfo->versionNo,
        ]);

        $entriesByTaxonomy = [];
        foreach ($entryAssignments as $entryAssignment) {
            $entry = $entryAssignment->getEntry();
            $taxonomy = $entry->getTaxonomy();

            $entriesByTaxonomy[$taxonomy][] = $entry;
        }

        $pathsStrings = [];
        foreach ($entryAssignments as $entryAssignment) {
            $path = $this->entryRepository->getPath($entryAssignment->getEntry());
            $pathsStrings[] = implode('/', array_map(static fn (TaxonomyEntry $entry) => $entry->getId(), $path)) . '/';
        }

        $fields = [
            new Search\Field(
                'taxonomy_entry_path',
                $pathsStrings,
                new Search\FieldType\MultipleIdentifierField(['raw' => true])
            ),
        ];
        foreach ($entriesByTaxonomy as $taxonomy => $entryIds) {
            $fields[] = new Search\Field(
                'taxonomy_entry_' . $taxonomy,
                array_map(
                    static fn (TaxonomyEntry $entry): int => $entry->getId(),
                    $entryIds,
                ),
                new Search\FieldType\MultipleIdentifierField(['raw' => true])
            );
        }

        return $fields;
    }
}
