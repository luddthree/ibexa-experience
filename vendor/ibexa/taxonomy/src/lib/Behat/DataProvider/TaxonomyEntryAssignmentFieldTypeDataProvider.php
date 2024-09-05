<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Behat\DataProvider;

use Ibexa\Behat\API\ContentData\FieldTypeData\FieldTypeDataProviderInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment\Value;
use Ibexa\Taxonomy\Service\TaxonomyService;

final class TaxonomyEntryAssignmentFieldTypeDataProvider implements FieldTypeDataProviderInterface
{
    private TaxonomyService $taxonomyService;

    public function __construct(TaxonomyService $taxonomyService)
    {
        $this->taxonomyService = $taxonomyService;
    }

    public function supports(string $fieldTypeIdentifier): bool
    {
        return $fieldTypeIdentifier === 'ibexa_taxonomy_entry_assignment';
    }

    public function generateData(
        string $contentTypeIdentifier,
        string $fieldIdentifier,
        string $language = 'eng-GB'
    ): Value {
        return new Value([$this->taxonomyService->loadEntryByIdentifier('root')]);
    }

    public function parseFromString(string $value): Value
    {
        $entries = array_map(function (string $tagIdentifier): TaxonomyEntry {
            return $this->taxonomyService->loadEntryByIdentifier($tagIdentifier, 'tags');
        }, explode(',', $value));

        return new Value($entries, 'tags');
    }
}
