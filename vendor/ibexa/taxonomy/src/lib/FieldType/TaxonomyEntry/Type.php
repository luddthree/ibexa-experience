<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\FieldType\TaxonomyEntry;

use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
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
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

final class Type extends FieldType implements LoggerAwareInterface, TranslationContainerInterface
{
    use LoggerAwareTrait;

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

    protected function createValueFromInput($inputValue)
    {
        if (is_array($inputValue)) {
            $inputValue = new Value($inputValue['taxonomy_entry']);
        }

        return $inputValue;
    }

    public function getFieldTypeIdentifier(): string
    {
        return $this->fieldTypeIdentifier;
    }

    /**
     * @param \Ibexa\Taxonomy\FieldType\TaxonomyEntry\Value $value
     */
    public function getName(SPIValue $value, FieldDefinition $fieldDefinition, string $languageCode): string
    {
        return $value->getTaxonomyEntry()->name ?? '';
    }

    /**
     * @return \Ibexa\Taxonomy\FieldType\TaxonomyEntry\Value
     */
    public function getEmptyValue(): SPIValue
    {
        return new Value();
    }

    public function fromHash($hash): SPIValue
    {
        if (is_array($hash) && isset($hash['taxonomy_entry'])) {
            $entry = $hash['taxonomy_entry'];
            if (!$entry instanceof TaxonomyEntry) {
                try {
                    $entry = $this->taxonomyService->loadEntryById($hash['taxonomy_entry']);
                } catch (TaxonomyEntryNotFoundException $e) {
                    $this->logger->error(
                        "Cannot find TaxonomyEntry with ID: {$hash['taxonomy_entry']}. "
                        . 'This may be an orphan Content item after corresponding TaxonomyEntry was removed.',
                        [
                            'exception' => $e,
                        ]
                    );
                    $entry = null;
                }
            }

            return new Value($entry);
        }

        return $this->getEmptyValue();
    }

    /**
     * @param \Ibexa\Taxonomy\FieldType\TaxonomyEntry\Value $value
     */
    protected function checkValueStructure(FieldTypeValue $value): void
    {
        if (!$value->getTaxonomyEntry() instanceof TaxonomyEntry) {
            throw new InvalidArgumentType(
                '$value->taxonomyEntry',
                TaxonomyEntry::class,
                $value->getTaxonomyEntry()
            );
        }
    }

    /**
     * @param \Ibexa\Taxonomy\FieldType\TaxonomyEntry\Value $value
     */
    public function isEmptyValue(SPIValue $value): bool
    {
        return $value->getTaxonomyEntry() === null;
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
     * @param \Ibexa\Taxonomy\FieldType\TaxonomyEntry\Value $value
     *
     * @return array<string, int|null>
     */
    public function toHash(SPIValue $value): array
    {
        $hash = ['taxonomy_entry' => null];
        $entry = $value->getTaxonomyEntry();

        if (null !== $entry) {
            $hash['taxonomy_entry'] = (int) $entry->id;
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
            Message::create('ibexa_taxonomy_entry.name', 'ibexa_fieldtypes')
                ->setDesc('Taxonomy Entry'),
        ];
    }
}
