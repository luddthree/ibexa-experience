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

/**
 * Options validator applicable to float and integer attribute types.
 */
final class NumericOptionsValidator implements OptionsValidatorInterface
{
    public function validateOptions(OptionsBag $options): array
    {
        $min = $options->get('min');
        $max = $options->get('max');

        if ($min !== null && $max !== null && $min > $max) {
            return [
                new OptionsValidatorError('[max]', 'Maximum value should be greater than minimum value'),
            ];
        }

        return [];
    }
}
