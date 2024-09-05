<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Behat\DataProvider;

use Ibexa\Behat\API\ContentData\FieldTypeData\FieldTypeDataProviderInterface;
use Ibexa\Taxonomy\FieldType\TaxonomyEntry\Value;
use Ibexa\Taxonomy\Service\TaxonomyService;

final class TaxonomyEntryFieldTypeDataProvider implements FieldTypeDataProviderInterface
{
    private TaxonomyService $taxonomyService;

    private const ROOT_TAG_IDENTIFIER = 'root';

    public function __construct(TaxonomyService $taxonomyService)
    {
        $this->taxonomyService = $taxonomyService;
    }

    public function supports(string $fieldTypeIdentifier): bool
    {
        return $fieldTypeIdentifier === 'ibexa_taxonomy_entry';
    }

    public function generateData(
        string $contentTypeIdentifier,
        string $fieldIdentifier,
        string $language = 'eng-GB'
    ): Value {
        return new Value($this->taxonomyService->loadEntryByIdentifier(self::ROOT_TAG_IDENTIFIER));
    }

    public function parseFromString(string $value): Value
    {
        return new Value($this->taxonomyService->loadEntryByIdentifier($value, 'tags'));
    }
}
