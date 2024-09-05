<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Seo\FieldType;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\Exception\InvalidArgumentType;
use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\FieldType;
use Ibexa\Core\FieldType\ValidationError;
use Ibexa\Seo\Value\SeoTypesValue;
use Ibexa\Seo\Value\SeoTypeValue;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

final class SeoType extends FieldType implements TranslationContainerInterface
{
    public const IDENTIFIER = 'ibexa_seo';

    public function __construct()
    {
        $this->settingsSchema = [
            'types' => [
                'type' => 'object',
                'default' => new SeoTypesValue(),
            ],
        ];
    }

    public function getFieldTypeIdentifier(): string
    {
        return self::IDENTIFIER;
    }

    public function getName(
        Value $value,
        FieldDefinition $fieldDefinition,
        string $languageCode
    ): string {
        return (string)$value;
    }

    protected function createValueFromInput($inputValue): SeoValue
    {
        if (!$inputValue instanceof SeoValue) {
            throw new InvalidArgumentException(
                '$inputValue',
                sprintf(
                    'Input value should be an instance of "%s", "%s" given instead',
                    SeoValue::class,
                    is_object($inputValue) ? get_class($inputValue) : gettype($inputValue)
                )
            );
        }

        return $inputValue;
    }

    public function getEmptyValue(): SeoValue
    {
        return new SeoValue(new SeoTypesValue());
    }

    public function fromHash($hash): SeoValue
    {
        if (!is_array($hash) ||
            !array_key_exists('value', $hash) ||
            !$hash['value'] instanceof SeoTypesValue
        ) {
            return $this->getEmptyValue();
        }

        $value = $hash['value'];

        return new SeoValue($value);
    }

    /**
     * @return array{value: \Ibexa\Contracts\Core\FieldType\Value}
     */
    public function toHash(Value $value)
    {
        return ['value' => $value];
    }

    protected static function checkValueType($value): void
    {
        if (!$value instanceof SeoValue) {
            throw new InvalidArgumentType(
                '$value',
                SeoValue::class,
                $value
            );
        }
    }

    protected function checkValueStructure(Value $value): void
    {
        self::checkValueType($value);
    }

    public function validate(FieldDefinition $fieldDefinition, Value $fieldValue): array
    {
        $errors = [];

        if ($this->isEmptyValue($fieldValue)) {
            return $errors;
        }

        return $errors;
    }

    /**
     * @param array<string, array<string, string>> $fieldSettings
     */
    public function validateFieldSettings($fieldSettings): array
    {
        $validationErrors = [];

        foreach ($fieldSettings as $name => $value) {
            $settingNameError = $this->validateSettingName($name);

            if ($settingNameError instanceof ValidationError) {
                $validationErrors[] = $settingNameError;
            }
        }

        return $validationErrors;
    }

    private function validateSettingName(string $name): ?ValidationError
    {
        /** @var array<string, array<string, string>> $settingsSchema */
        $settingsSchema = $this->settingsSchema;

        if (isset($settingsSchema[$name])) {
            return null;
        }

        return new ValidationError(
            "Setting '%setting%' is unknown",
            null,
            [
                '%setting%' => $name,
            ],
            "[$name]"
        );
    }

    public static function getTranslationMessages(): array
    {
        return [
            Message::create(self::IDENTIFIER . '.name', 'ibexa_fieldtypes')->setDesc('SEO'),
        ];
    }

    /**
     * @param array{types: array<array{type: string, fields: array<string, string>}>} $fieldSettingsHash
     *
     * @return array{types: \Ibexa\Seo\Value\SeoTypesValue}|array{}
     */
    public function fieldSettingsFromHash($fieldSettingsHash): array
    {
        if (!isset($fieldSettingsHash['types'])) {
            return [];
        }

        $types = new SeoTypesValue();
        foreach ($fieldSettingsHash['types'] as $typeName => $typeHash) {
            $types->setType($typeName, new SeoTypeValue($typeHash['type'], $typeHash['fields']));
        }

        return ['types' => $types];
    }

    /**
     * @param array{types: \Ibexa\Seo\Value\SeoTypesValue} $fieldSettings
     *
     * @return array{types: array<array{type: string|null, fields: array<string, string|null>}>}|array{}
     */
    public function fieldSettingsToHash($fieldSettings): array
    {
        if (!isset($fieldSettings['types']) || !($fieldSettings['types'] instanceof SeoTypesValue)) {
            return [];
        }

        return [
            'types' => array_map(
                static fn (SeoTypeValue $seoTypeValue): array => $seoTypeValue->jsonSerialize(),
                $fieldSettings['types']->getTypes()
            ),
        ];
    }
}
