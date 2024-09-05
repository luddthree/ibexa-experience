<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements DataTransformerInterface<PriceInterface, string|int>
 */
final class ProductPriceTransformer implements DataTransformerInterface
{
    private ProductPriceServiceInterface $productPriceService;

    public function __construct(ProductPriceServiceInterface $productPriceService)
    {
        $this->productPriceService = $productPriceService;
    }

    public function transform($value): ?int
    {
        if (null === $value) {
            return null;
        }

        if (!($value instanceof PriceInterface)) {
            throw new TransformationFailedException('Expected a ' . PriceInterface::class . ' object.');
        }

        return $value->getId();
    }

    public function reverseTransform($value): ?PriceInterface
    {
        if (empty($value)) {
            return null;
        }

        if (!is_int($value)) {
            throw new TransformationFailedException('Invalid data, expected an integer value');
        }

        try {
            return $this->productPriceService->getPriceById($value);
        } catch (NotFoundException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
