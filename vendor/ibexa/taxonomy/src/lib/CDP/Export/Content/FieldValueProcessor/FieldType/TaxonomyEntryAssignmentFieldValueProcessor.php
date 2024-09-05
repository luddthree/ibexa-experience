<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\CDP\Export\Content\FieldValueProcessor\FieldType;

use Ibexa\Contracts\Cdp\Export\Content\FieldValueProcessorInterface;
use Ibexa\Contracts\Core\FieldType\FieldType;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;

final class TaxonomyEntryAssignmentFieldValueProcessor implements FieldValueProcessorInterface
{
    private FieldType $fieldType;

    private string $wrapPattern;

    public function __construct(
        FieldType $fieldType,
        string $wrapPattern
    ) {
        $this->fieldType = $fieldType;
        $this->wrapPattern = $wrapPattern;
    }

    public function process(Field $field, Content $content): array
    {
        /** @var \Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment\Value $value */
        $value = $field->getValue();

        $taxonomyEntries = $value->getTaxonomyEntries();

        $entryIds = $this->wrapValues(array_map(
            static fn (TaxonomyEntry $taxonomyEntry): string => (string) $taxonomyEntry->getId(),
            $taxonomyEntries
        ));
        $entryIdentifiers = $this->wrapValues(array_column($taxonomyEntries, 'identifier'));
        $entryNames = $this->wrapValues(array_column($taxonomyEntries, 'name'));

        return [
            'value_entries_id' => implode(', ', $entryIds),
            'value_entries_identifier' => implode(', ', $entryIdentifiers),
            'value_entries_name' => implode(', ', $entryNames),
            'value_taxonomy' => $value->getTaxonomy() ?? '',
        ];
    }

    public function supports(Field $field, Content $content): bool
    {
        return $this->fieldType->getFieldTypeIdentifier() === $field->fieldTypeIdentifier;
    }

    /**
     * @param array<string> $values
     *
     * @return array<string>
     */
    private function wrapValues(array $values): array
    {
        return array_map(
            fn (string $value): string => sprintf($this->wrapPattern, $value),
            $values
        );
    }
}
