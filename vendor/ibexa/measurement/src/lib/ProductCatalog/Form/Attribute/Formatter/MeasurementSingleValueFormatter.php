<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Form\Attribute\Formatter;

use Ibexa\Contracts\Measurement\Formatter\MeasurementSignFormatterInterface;
use Ibexa\Contracts\Measurement\Formatter\MeasurementValueFormatterInterface;
use Ibexa\Contracts\Measurement\Value\Definition\Sign;
use Ibexa\Contracts\Measurement\Value\SimpleValueInterface;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueFormatterInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;

final class MeasurementSingleValueFormatter implements ValueFormatterInterface
{
    private MeasurementValueFormatterInterface $valueFormatter;

    private MeasurementSignFormatterInterface $signFormatter;

    public function __construct(
        MeasurementValueFormatterInterface $valueFormatter,
        MeasurementSignFormatterInterface $signFormatter
    ) {
        $this->valueFormatter = $valueFormatter;
        $this->signFormatter = $signFormatter;
    }

    public function formatValue(AttributeInterface $attribute, array $parameters = []): ?string
    {
        $value = $attribute->getValue();

        if ($value instanceof SimpleValueInterface) {
            $formattedValue = $this->valueFormatter->format($value);

            $sign = $this->getValueSign($attribute);
            if ($sign !== Sign::SIGN_NONE) {
                $formattedValue = $this->signFormatter->format($sign) . ' ' . $formattedValue;
            }

            return $formattedValue;
        }

        return null;
    }

    private function getValueSign(AttributeInterface $attribute): string
    {
        return $attribute->getAttributeDefinition()->getOptions()->get('sign');
    }
}
