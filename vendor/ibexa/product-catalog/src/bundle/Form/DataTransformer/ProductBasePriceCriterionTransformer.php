<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\CatalogFilterPriceData;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\BasePriceRange;
use Ibexa\ProductCatalog\Money\DecimalMoneyFactory;
use Money\Currency;
use Money\Money;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements DataTransformerInterface<
 *     \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\BasePriceRange,
 *     \Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\CatalogFilterPriceData
 * >
 */
final class ProductBasePriceCriterionTransformer implements DataTransformerInterface
{
    private DecimalMoneyFactory $decimalMoneyParserFactory;

    public function __construct(DecimalMoneyFactory $decimalMoneyParserFactory)
    {
        $this->decimalMoneyParserFactory = $decimalMoneyParserFactory;
    }

    public function transform($value): ?CatalogFilterPriceData
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof BasePriceRange) {
            throw new TransformationFailedException(
                sprintf(
                    'Expected a %s object, received %s.',
                    BasePriceRange::class,
                    get_debug_type($value)
                )
            );
        }

        return new CatalogFilterPriceData(
            $this->getCurrency($value),
            (float)$this->getFormattedPrice($value->getMin()),
            (float)$this->getFormattedPrice($value->getMax())
        );
    }

    public function reverseTransform($value): ?BasePriceRange
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof CatalogFilterPriceData) {
            throw new TransformationFailedException(
                sprintf(
                    'Invalid data, expected a %s value, received %s.',
                    CatalogFilterPriceData::class,
                    get_debug_type($value)
                )
            );
        }

        $currency = $value->getCurrency();
        $minPrice = $value->getMinPrice();
        $maxPrice = $value->getMaxPrice();

        if ($currency === null || ($minPrice === null && $maxPrice === null)) {
            return null;
        }

        return new BasePriceRange(
            $this->parsePrice($minPrice, $currency),
            $this->parsePrice($maxPrice, $currency)
        );
    }

    private function getCurrency(BasePriceRange $value): ?Currency
    {
        $basePrice = $value->getMin() ?? $value->getMax() ?? null;

        if ($basePrice === null) {
            return null;
        }

        return $basePrice->getCurrency();
    }

    private function getFormattedPrice(?Money $basePrice): ?string
    {
        $formatter = $this->decimalMoneyParserFactory->getMoneyFormatter();

        return $basePrice ? $formatter->format($basePrice) : null;
    }

    private function parsePrice(?float $value, Currency $currency): ?Money
    {
        $parser = $this->decimalMoneyParserFactory->getMoneyParser();

        if ($value === null) {
            return null;
        }

        return $parser->parse((string)$value, $currency);
    }
}
