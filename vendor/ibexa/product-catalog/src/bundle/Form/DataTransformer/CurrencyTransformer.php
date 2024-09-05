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
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements DataTransformerInterface<CurrencyInterface, string>
 */
final class CurrencyTransformer implements DataTransformerInterface
{
    private CurrencyServiceInterface $currencyService;

    public function __construct(CurrencyServiceInterface $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function transform($value): ?string
    {
        if (null === $value) {
            return null;
        }

        if (!($value instanceof CurrencyInterface)) {
            throw new TransformationFailedException('Expected a ' . CurrencyInterface::class . ' object.');
        }

        return $value->getCode();
    }

    public function reverseTransform($value): ?CurrencyInterface
    {
        if (empty($value)) {
            return null;
        }

        if (!is_string($value)) {
            throw new TransformationFailedException('Invalid data, expected a string value');
        }

        try {
            return $this->currencyService->getCurrencyByCode($value);
        } catch (NotFoundException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
