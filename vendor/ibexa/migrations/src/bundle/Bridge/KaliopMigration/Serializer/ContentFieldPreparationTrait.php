<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer;

trait ContentFieldPreparationTrait
{
    /**
     * @see \Ibexa\Bundle\Migration\Serializer\Normalizer\Content\FieldNormalizer::denormalize
     *
     * @param array<mixed> $data
     *
     * @return array<array{
     *     fieldDefIdentifier: string,
     *     languageCode: ?string,
     *     value: mixed,
     * }>
     */
    private function prepareFields(array $data): array
    {
        $fields = [];
        $attributes = $data['attributes'] ?? [];

        $isLanguagePrefixed = $this->areFieldsPrefixedWithLanguage($attributes);

        foreach ($attributes as $fieldName => $value) {
            if ($isLanguagePrefixed) {
                // Language-based fields
                foreach ($value as $languageCode => $fieldValue) {
                    $fields[] = [
                        'fieldDefIdentifier' => $fieldName,
                        'languageCode' => $languageCode,
                        'value' => $fieldValue,
                    ];
                }
            } else {
                // Simple declaration fields
                $fields[] = [
                    'fieldDefIdentifier' => $fieldName,
                    'languageCode' => $data['lang'] ?? null,
                    'value' => $value,
                ];
            }
        }

        return $fields;
    }

    /**
     * @param array<mixed> $attributes
     */
    private function areFieldsPrefixedWithLanguage(array $attributes): bool
    {
        foreach ($attributes as $fieldName => $value) {
            if (!is_array($value)) {
                return false;
            }

            foreach ($value as $potentialLang => $fieldValue) {
                if (!is_string($potentialLang)) {
                    return false;
                }
            }
        }

        return true;
    }
}

class_alias(ContentFieldPreparationTrait::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\Serializer\ContentFieldPreparationTrait');
