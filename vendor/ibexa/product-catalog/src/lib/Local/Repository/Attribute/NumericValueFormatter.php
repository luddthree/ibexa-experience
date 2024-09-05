<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Attribute;

use Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueFormatterInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;
use Ibexa\ProductCatalog\NumberFormatter\NumberFormatterFactoryInterface;

final class NumericValueFormatter implements ValueFormatterInterface
{
    private NumberFormatterFactoryInterface $numberFormatterFactory;

    public function __construct(NumberFormatterFactoryInterface $numberFormatterFactory)
    {
        $this->numberFormatterFactory = $numberFormatterFactory;
    }

    public function formatValue(AttributeInterface $attribute, array $parameters = []): ?string
    {
        $value = $attribute->getValue();
        if ($value === null) {
            return null;
        }

        $formatter = $parameters['formatter'] ?? null;
        if ($formatter === null) {
            $formatter = $this->numberFormatterFactory->createNumberFormatter(
                $parameters['locale'] ?? null
            );
        }

        return $formatter->format($value);
    }
}
