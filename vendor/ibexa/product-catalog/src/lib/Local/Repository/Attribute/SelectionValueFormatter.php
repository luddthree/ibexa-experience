<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Attribute;

use Ibexa\Contracts\Core\Repository\LanguageResolver;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueFormatterInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;

/**
 * @phpstan-type Choice array{value: string, label: array<string, string>}
 */
final class SelectionValueFormatter implements ValueFormatterInterface
{
    private LanguageResolver $languageResolver;

    public function __construct(LanguageResolver $languageResolver)
    {
        $this->languageResolver = $languageResolver;
    }

    public function formatValue(AttributeInterface $attribute, array $parameters = []): ?string
    {
        $value = $attribute->getValue();
        if ($value === null) {
            return null;
        }

        $labels = $this->getLabelsForValue($this->getChoices($attribute), $value);
        if (!empty($labels)) {
            $languages = $this->languageResolver->getPrioritizedLanguages($parameters['languages'] ?? null);
            foreach ($languages as $language) {
                if (isset($labels[$language])) {
                    return $labels[$language];
                }
            }
        }

        return $value;
    }

    /**
     * @return Choice[]
     */
    private function getChoices(AttributeInterface $attribute): array
    {
        return $attribute->getAttributeDefinition()->getOptions()->get('choices', []);
    }

    /**
     * @param Choice[] $choices
     *
     * @return string[]
     */
    private function getLabelsForValue(array $choices, string $value): array
    {
        foreach ($choices as $choice) {
            if ($choice['value'] === $value) {
                return $choice['label'];
            }
        }

        return [];
    }
}
