<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\ContentType;

use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition as APIFieldDefinition;
use Ibexa\Migration\ValueObject\ValueObjectInterface;

final class FieldDefinition implements ValueObjectInterface
{
    /** @var string */
    public $identifier;

    /** @var ?string */
    public $newIdentifier;

    /** @var string */
    public $type;

    /** @var ?int */
    public $position;

    /**
     * @var array<string, array{
     *     name: string,
     *     description?: string,
     * }>
     */
    public $translations;

    /** @var ?bool */
    public $required;

    /** @var ?bool */
    public $searchable;

    /** @var ?bool */
    public $infoCollector;

    /** @var ?bool */
    public $translatable;

    /** @var ?bool */
    public $thumbnail;

    /** @var ?string */
    public $category;

    /** @var mixed */
    public $defaultValue;

    /** @var array<mixed>|null */
    public $fieldSettings;

    /** @var array<mixed>|null */
    public $validatorConfiguration;

    /**
     * @param mixed $defaultValue
     */
    public static function fromAPIFieldDefinition(APIFieldDefinition $fieldDefinition, $defaultValue): self
    {
        $vo = new self();
        $vo->identifier = $fieldDefinition->identifier;
        $vo->type = $fieldDefinition->fieldTypeIdentifier;
        $vo->position = $fieldDefinition->position;
        $vo->translations = self::prepareTranslations($fieldDefinition);
        $vo->required = $fieldDefinition->isRequired;
        $vo->searchable = $fieldDefinition->isSearchable;
        $vo->infoCollector = $fieldDefinition->isInfoCollector;
        $vo->translatable = $fieldDefinition->isTranslatable;
        $vo->thumbnail = $fieldDefinition->isThumbnail;
        $vo->category = $fieldDefinition->fieldGroup;
        $vo->defaultValue = $defaultValue;
        $vo->fieldSettings = $fieldDefinition->fieldSettings;
        $vo->validatorConfiguration = $fieldDefinition->getValidatorConfiguration();

        return $vo;
    }

    /**
     * @param array<mixed> $data
     */
    public static function createFromArray(array $data): self
    {
        $vo = new self();
        $vo->identifier = $data['identifier'];
        $vo->newIdentifier = $data['newIdentifier'] ?? null;
        $vo->type = $data['type'];
        $vo->position = $data['position'] ?? null;
        $vo->translations = $data['translations'];
        $vo->required = $data['required'] ?? null;
        $vo->searchable = $data['searchable'] ?? null;
        $vo->infoCollector = $data['infoCollector'] ?? null;
        $vo->translatable = $data['translatable'] ?? null;
        $vo->thumbnail = $data['thumbnail'] ?? null;
        $vo->category = $data['category'] ?? null;
        $vo->defaultValue = $data['defaultValue'] ?? null;
        $vo->fieldSettings = $data['fieldSettings'] ?? null;
        $vo->validatorConfiguration = $data['validatorConfiguration'] ?? null;

        return $vo;
    }

    /**
     * @return array<string, array{
     *     name: string,
     *     description?: string,
     * }>
     */
    private static function prepareTranslations(APIFieldDefinition $fieldDefinition): array
    {
        $translations = [];

        foreach ($fieldDefinition->getNames() as $lang => $value) {
            $translations[$lang]['name'] = $value ?? '';
        }

        foreach ($fieldDefinition->getDescriptions() as $lang => $value) {
            $translations[$lang]['description'] = $value ?? '';
        }

        return $translations;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getNewIdentifier(): ?string
    {
        return $this->newIdentifier;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * @return array<string, array{
     *     name: string,
     *     description: string,
     * }>
     */
    public function getTranslations(): array
    {
        return $this->translations;
    }

    public function isRequired(): ?bool
    {
        return $this->required;
    }

    public function isSearchable(): ?bool
    {
        return $this->searchable;
    }

    public function isInfoCollector(): ?bool
    {
        return $this->infoCollector;
    }

    public function isTranslatable(): ?bool
    {
        return $this->translatable;
    }

    public function isThumbnail(): ?bool
    {
        return $this->thumbnail;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @return array<mixed>|null
     */
    public function getFieldSettings(): ?array
    {
        return $this->fieldSettings;
    }

    /**
     * @return array<mixed>|null
     */
    public function getValidatorConfiguration(): ?array
    {
        return $this->validatorConfiguration;
    }
}

class_alias(FieldDefinition::class, 'Ibexa\Platform\Migration\ValueObject\ContentType\FieldDefinition');
