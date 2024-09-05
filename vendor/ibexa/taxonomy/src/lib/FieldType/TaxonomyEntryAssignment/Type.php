<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment;

use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue as PersistenceValue;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\FieldType\FieldType;
use Ibexa\Core\FieldType\ValidationError;
use Ibexa\Core\FieldType\Value as FieldTypeValue;
use Ibexa\Taxonomy\Exception\TaxonomyEntryNotFoundException;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

final class Type extends FieldType implements TranslationContainerInterface
{
    public const FIELD_SETTING_TAXONOMY = 'taxonomy';

    private string $fieldTypeIdentifier;

    private TaxonomyServiceInterface $taxonomyService;

    private TaxonomyConfiguration $taxonomyConfiguration;

    protected $settingsSchema = [
        self::FIELD_SETTING_TAXONOMY => [
            'type' => 'choice',
            'default' => null,
        ],
    ];

    public function __construct(
        string $fieldTypeIdentifier,
        TaxonomyServiceInterface $taxonomyService,
        TaxonomyConfiguration $taxonomyConfiguration
    ) {
        $this->fieldTypeIdentifier = $fieldTypeIdentifier;
        $this->taxonomyService = $taxonomyService;
        $this->taxonomyConfiguration = $taxonomyConfiguration;
    }

    protected function getSortInfo(FieldTypeValue $value): string
    {
        return '';
    }

    /**
     * @param \Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment\Value $value
     */
    public function toPersistenceValue(SPIValue $value): FieldValue
    {
        return new FieldValue(
            [
                'data' => ['taxonomy' => $value->getTaxonomy()],
                'externalData' => $this->toHash($value),
                'sortKey' => null,
            ]
        );
    }

    public function fromPersistenceValue(PersistenceValue $fieldValue): Value
    {
        return $this->fromHash($fieldValue->externalData);
    }

    protected function createValueFromInput($inputValue): Value
    {
        if (is_array($inputValue)) {
            $inputValue = new Value($inputValue['taxonomy_entries'], $inputValue['taxonomy']);
        }

        return $inputValue;
    }

    public function getFieldTypeIdentifier(): string
    {
        return $this->fieldTypeIdentifier;
    }

    /**
     * @param \Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment\Value $value
     */
    public function getName(SPIValue $value, FieldDefinition $fieldDefinition, string $languageCode): string
    {
        return (string) $value;
    }

    public function getEmptyValue(): Value
    {
        return new Value();
    }

    public function fromHash($hash): Value
    {
        if (
            !is_array($hash)
            || !isset($hash['taxonomy_entries'])
            || !isset($hash['taxonomy'])
        ) {
            return $this->getEmptyValue();
        }

        $entries = [];
        foreach ($hash['taxonomy_entries'] as $entry) {
            if (!$entry instanceof TaxonomyEntry) {
                try {
                    $entry = $this->taxonomyService->loadEntryById((int)$entry);
                } catch (TaxonomyEntryNotFoundException $e) {
                    continue;
                }
            }
            $entries[] = $entry;
        }

        return new Value($entries, $hash['taxonomy']);
    }

    /**
     * @param \Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment\Value $value
     *
     * @return array<\Ibexa\Contracts\Core\FieldType\ValidationError>
     */
    public function validate(FieldDefinition $fieldDefinition, SPIValue $value): array
    {
        $errors = [];
        $lastCheckedTaxonomy = null;

        foreach ($value->getTaxonomyEntries() as $taxonomyEntry) {
            if (null !== $lastCheckedTaxonomy && $taxonomyEntry->taxonomy !== $lastCheckedTaxonomy) {
                $errors[] = new ValidationError('You can only select Entries belonging to the same taxonomy.');

                break;
            }

            $lastCheckedTaxonomy = $taxonomyEntry->taxonomy;
        }

        return $errors;
    }

    /**
     * @param \Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment\Value $value
     */
    protected function checkValueStructure(FieldTypeValue $value): void
    {
        foreach ($value->getTaxonomyEntries() as $entry) {
            if (!$entry instanceof TaxonomyEntry) {
                throw new InvalidArgumentType(
                    '$value->taxonomyEntries',
                    TaxonomyEntry::class,
                    $value->getTaxonomyEntries()
                );
            }
        }
    }

    /**
     * @param \Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment\Value $value
     */
    public function isEmptyValue(SPIValue $value): bool
    {
        return $value->getTaxonomy() === null && empty($value->getTaxonomyEntries());
    }

    /**
     * @param array<string, mixed> $fieldSettings
     *
     * @return array<ValidationError>
     */
    public function validateFieldSettings($fieldSettings): array
    {
        $taxonomies = $this->taxonomyConfiguration->getTaxonomies();
        $validationErrors = [];

        foreach ($fieldSettings as $name => $value) {
            if (!isset($this->settingsSchema[$name])) {
                $validationErrors[] = new ValidationError(
                    "Setting '%setting%' is unknown",
                    null,
                    [
                        '%setting%' => $name,
                    ],
                    "[$name]"
                );
                continue;
            }

            if ($name !== self::FIELD_SETTING_TAXONOMY) {
                continue;
            }

            if ($value !== null && !in_array($value, $taxonomies, true)) {
                $validationErrors[] = new ValidationError(
                    "Setting '%setting%' has invalid value. Allowed values are: %allowedValues%.",
                    null,
                    [
                        '%setting%' => $name,
                        '%allowedValues%' => implode(', ', $taxonomies),
                    ],
                    "[$name]"
                );
            }
        }

        return $validationErrors;
    }

    /**
     * @param \Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment\Value $value
     *
     * @return array{
     *     taxonomy_entries: array<int, int>,
     *     taxonomy: string|null,
     * }
     */
    public function toHash(SPIValue $value): array
    {
        $hash = [
            'taxonomy_entries' => [],
            'taxonomy' => $value->getTaxonomy(),
        ];

        foreach ($value->getTaxonomyEntries() as $entry) {
            $hash['taxonomy_entries'][] = $entry->id;
        }

        return $hash;
    }

    public function isSearchable(): bool
    {
        return true;
    }

    public static function getTranslationMessages(): array
    {
        return [
            Message::create('ibexa_taxonomy_entry_assignment.name', 'ibexa_fieldtypes')
                ->setDesc('Taxonomy Entry Assignment'),
        ];
    }
}
