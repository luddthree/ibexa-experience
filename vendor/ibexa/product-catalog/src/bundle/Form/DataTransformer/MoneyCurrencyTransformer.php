<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Money\Currency;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements DataTransformerInterface<Currency, CurrencyInterface>
 */
final class MoneyCurrencyTransformer implements DataTransformerInterface
{
    private CurrencyServiceInterface $currencyService;

    public function __construct(CurrencyServiceInterface $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function transform($value): ?CurrencyInterface
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof Currency) {
            throw new TransformationFailedException('Expected a ' . Currency::class . ' object.');
        }

        try {
            return $this->currencyService->getCurrencyByCode($value->getCode());
        } catch (NotFoundException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function reverseTransform($value): ?Currency
    {
        if (empty($value)) {
            return null;
        }

        if (!$value instanceof CurrencyInterface) {
            throw new TransformationFailedException('Expected a ' . CurrencyInterface::class . ' object.');
        }

        return new Currency($value->getCode());
    }
}
