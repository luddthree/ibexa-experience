<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Attribute;

use Ibexa\Contracts\Core\Options\OptionsBag;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\OptionsValidatorError;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\OptionsValidatorInterface;

final class SelectionOptionsValidator implements OptionsValidatorInterface
{
    public function validateOptions(OptionsBag $options): array
    {
        $choices = $options->get('choices');
        if (empty($choices)) {
            return [
                new OptionsValidatorError('[choices]', 'At least one option is required'),
            ];
        }

        $visitedValues = [];
        $errors = [];
        foreach ($choices as $key => $choice) {
            $value = $choice['value'] ?? null;
            $labels = $choice['label'] ?? null;

            if ($value !== null) {
                $value = (string)$value;
            }

            if ($this->isBlank($value)) {
                $errors[] = new OptionsValidatorError("[choices][$key][value]", 'Value should not be blank');
            } elseif (in_array($value, $visitedValues, true)) {
                $errors[] = new OptionsValidatorError("[choices][$key][value]", 'Duplicated value');
            } else {
                $visitedValues[] = $value;
            }

            if ($labels !== null) {
                foreach ($labels as $languageCode => $label) {
                    if ($this->isBlank($label)) {
                        $errors[] = new OptionsValidatorError("[choices][$key][label][$languageCode]", 'Label should not be blank');
                    }
                }
            } else {
                $errors[] = new OptionsValidatorError("[choices][$key][label]", 'Label should not be blank');
            }
        }

        return $errors;
    }

    private function isBlank(?string $value): bool
    {
        if ($value === null) {
            return true;
        }

        return trim($value) === '';
    }
}
