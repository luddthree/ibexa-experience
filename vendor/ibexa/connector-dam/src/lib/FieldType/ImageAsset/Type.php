<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam\FieldType\ImageAsset;

use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\FieldType\FieldType;
use Ibexa\Core\FieldType\ImageAsset\Value as ImageAssetValue;
use Ibexa\Core\FieldType\Value as BaseValue;

class Type extends FieldType
{
    /** @var \Ibexa\Core\FieldType\FieldType */
    private $innerFieldType;

    public function __construct(
        FieldType $innerFieldType
    ) {
        $this->innerFieldType = $innerFieldType;
    }

    /**
     * @param \Ibexa\Connector\Dam\FieldType\ImageAsset\Value $fieldValue
     *
     * @return \Ibexa\Contracts\Core\FieldType\ValidationError[]
     */
    public function validate(FieldDefinition $fieldDefinition, SPIValue $fieldValue): array
    {
        if (isset($fieldValue->source)) {
            return [];
        }

        return $this->innerFieldType->validate($fieldDefinition, $fieldValue);
    }

    public function getFieldTypeIdentifier(): string
    {
        return $this->innerFieldType->getFieldTypeIdentifier();
    }

    /**
     * @param \Ibexa\Connector\Dam\FieldType\ImageAsset\Value $value
     */
    public function getName(SPIValue $value, FieldDefinition $fieldDefinition, string $languageCode): string
    {
        if (empty($value->destinationContentId)) {
            return '';
        }

        if (isset($value->source)) {
            return $value->destinationContentId . '/' . $value->source;
        }

        return $this->innerFieldType->getName($value, $fieldDefinition, $languageCode);
    }

    public function getEmptyValue(): Value
    {
        return new Value();
    }

    /**
     * @param \Ibexa\Connector\Dam\FieldType\ImageAsset\Value $value
     */
    public function isEmptyValue(SPIValue $value): bool
    {
        return null === $value->destinationContentId;
    }

    /**
     * @param int|string|\Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo|\Ibexa\Core\FieldType\Relation\Value $inputValue
     *
     * @return \Ibexa\Connector\Dam\FieldType\ImageAsset\Value
     */
    protected function createValueFromInput($inputValue)
    {
        if ($inputValue instanceof ContentInfo) {
            $inputValue = new Value($inputValue->id);
        } elseif (\is_object($inputValue) && \get_class($inputValue) === ImageAssetValue::class) {
            $inputValue = new Value($inputValue->destinationContentId);
        } elseif (\is_int($inputValue) || \is_string($inputValue)) {
            $inputValue = new Value($inputValue);
        } elseif (\is_array($inputValue)) {
            $inputValue = $this->fromHash($inputValue);
        }

        return $inputValue;
    }

    /**
     * @param \Ibexa\Connector\Dam\FieldType\ImageAsset\Value $value
     */
    protected function checkValueStructure(BaseValue $value): void
    {
        if (!\is_int($value->destinationContentId) && !\is_string($value->destinationContentId)) {
            throw new InvalidArgumentType('$value->destinationContentId', 'string|int', $value->destinationContentId);
        }

        if ($value->alternativeText !== null && !\is_string($value->alternativeText)) {
            throw new InvalidArgumentType('$value->alternativeText', 'string|null', $value->alternativeText);
        }

        if ($value->source !== null && !\is_string($value->source)) {
            throw new InvalidArgumentType('$value->source', 'string|null', $value->source);
        }
    }

    protected function getSortInfo(BaseValue $value): bool
    {
        return false;
    }

    /**
     * @param mixed $hash
     */
    public function fromHash($hash): Value
    {
        if (!$hash) {
            return new Value();
        }

        return new Value(
            $hash['destinationContentId'],
            $hash['alternativeText'] ?? null,
            $hash['source'] ?? null
        );
    }

    /**
     * @param \Ibexa\Connector\Dam\FieldType\ImageAsset\Value$value
     */
    public function toHash(SPIValue $value): array
    {
        $destinationContentId = null;

        return [
            'destinationContentId' => $value->destinationContentId,
            'alternativeText' => $value->alternativeText,
            'source' => $value->source,
        ];
    }

    /**
     * @param \Ibexa\Connector\Dam\FieldType\ImageAsset\Value $fieldValue
     *
     * @return array Hash with relation type as key and array of destination content ids as value.
     */
    public function getRelations(SPIValue $fieldValue): array
    {
        if ($fieldValue->source) {
            return [];
        }

        return $this->innerFieldType->getRelations($fieldValue);
    }

    /**
     * Returns whether the field type is searchable.
     */
    public function isSearchable(): bool
    {
        return $this->innerFieldType->isSearchable();
    }
}

class_alias(Type::class, 'Ibexa\Platform\Connector\Dam\FieldType\ImageAsset\Type');
