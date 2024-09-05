<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypeAddress\FieldType;

use Ibexa\Contracts\Core\FieldType\Value as BaseValue;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\FieldType\FieldType;
use Ibexa\Core\FieldType\ValidationError;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Contracts\Translation\TranslatorInterface;

final class Type extends FieldType
{
    protected $settingsSchema = [
        'type' => [
            'type' => 'string',
            'default' => 'personal',
        ],
    ];

    private string $fieldTypeIdentifier;

    private TranslatorInterface $translator;

    public function __construct(
        string $fieldTypeIdentifier,
        TranslatorInterface $translator
    ) {
        $this->fieldTypeIdentifier = $fieldTypeIdentifier;
        $this->translator = $translator;
    }

    protected function createValueFromInput($inputValue)
    {
        return $inputValue;
    }

    public function getFieldTypeIdentifier()
    {
        return $this->fieldTypeIdentifier;
    }

    public function getName(
        BaseValue $value,
        FieldDefinition $fieldDefinition,
        string $languageCode
    ): string {
        return (string)$value;
    }

    public function getEmptyValue(): Value
    {
        return new Value();
    }

    protected function checkValueStructure(BaseValue $value): void
    {
        if (!$value instanceof Value) {
            throw new InvalidArgumentType(
                '$value',
                Value::class,
                $value
            );
        }
    }

    public function fromHash($hash): Value
    {
        $name = $hash['name'] ?? null;
        $country = $hash['country'] ?? null;
        $fields = $hash['fields'] ?? [];

        return new Value($name, $country, $fields);
    }

    /**
     * @param \Ibexa\FieldTypeAddress\FieldType\Value $value
     */
    public function toHash(BaseValue $value): ?array
    {
        return [
            'name' => $value->name,
            'country' => $value->country,
            'fields' => $value->fields,
        ];
    }

    public function validateFieldSettings($fieldSettings)
    {
        return [];
    }

    /**
     * @return \Ibexa\Contracts\Core\FieldType\ValidationError[]
     */
    public function validate(
        FieldDefinition $fieldDefinition,
        BaseValue $value
    ): array {
        if (!$value instanceof Value || $this->isEmptyValue($value)) {
            return parent::validate($fieldDefinition, $value);
        }

        $validationErrors = [];

        if ($fieldDefinition->isRequired && empty($value->name)) {
            $validationErrors[] = new ValidationError(
                $this->translator->trans(
                    /** @Desc("Name cannot be empty.")' */
                    'ibexa.address.name.validation.empty',
                    [],
                    'ibexa_fieldtype_address'
                ),
                null,
                [],
                '[name]'
            );
        }

        if ($fieldDefinition->isRequired && empty($value->country)) {
            $validationErrors[] = new ValidationError(
                $this->translator->trans(
                    /** @Desc("Country cannot be empty.")' */
                    'ibexa.address.country.validation.empty',
                    [],
                    'ibexa_fieldtype_address'
                ),
                null,
                [],
                '[country]'
            );
        }

        return $validationErrors;
    }
}
