<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment;

use Ibexa\Contracts\Core\FieldType\Indexable as IndexableInterface;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Contracts\Core\Search;
use Ibexa\Taxonomy\Persistence\Repository\TaxonomyEntryRepository;

final class Indexable implements IndexableInterface
{
    private TaxonomyEntryRepository $taxonomyEntryRepository;

    public function __construct(TaxonomyEntryRepository $taxonomyEntryRepository)
    {
        $this->taxonomyEntryRepository = $taxonomyEntryRepository;
    }

    /**
     * @return array<\Ibexa\Contracts\Core\Search\Field>
     */
    public function getIndexData(Field $field, FieldDefinition $fieldDefinition): array
    {
        $taxonomyEntries = $field->value->externalData['taxonomy_entries'] ?? null;
        if ($taxonomyEntries === null) {
            return [];
        }

        $searchFields = [
            new Search\Field(
                'id',
                $taxonomyEntries,
                new Search\FieldType\MultipleIntegerField()
            ),
            new Search\Field(
                'count',
                count($taxonomyEntries),
                new Search\FieldType\IntegerField()
            ),
            new Search\Field(
                'sort_value',
                implode('-', $taxonomyEntries),
                new Search\FieldType\StringField()
            ),
        ];

        /** @var list<\Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntry> $entries */
        $entries = $this->taxonomyEntryRepository->findBy(['id' => $taxonomyEntries]);
        $fullTextValues = [];
        foreach ($entries as $entryEntity) {
            $fullTextValues[] = $entryEntity->getNames()[$field->languageCode] ?? $entryEntity->getName();
        }
        $searchFields[] = new Search\Field(
            'fulltext',
            $fullTextValues,
            new Search\FieldType\FullTextField()
        );

        return $searchFields;
    }

    /**
     * @return array<string, \Ibexa\Contracts\Core\Search\FieldType>
     */
    public function getIndexDefinition(): array
    {
        return [
            'id' => new Search\FieldType\MultipleIntegerField(),
            'count' => new Search\FieldType\IntegerField(),
            'sort_value' => new Search\FieldType\StringField(),
        ];
    }

    public function getDefaultMatchField(): string
    {
        return 'id';
    }

    public function getDefaultSortField(): string
    {
        return 'sort_value';
    }
}
