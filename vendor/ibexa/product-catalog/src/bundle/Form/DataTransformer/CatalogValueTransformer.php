<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\ProductCatalog\CatalogServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements DataTransformerInterface<?int, ?\Ibexa\Contracts\ProductCatalog\Values\CatalogInterface>
 */
final class CatalogValueTransformer implements DataTransformerInterface
{
    private CatalogServiceInterface $catalogService;

    public function __construct(CatalogServiceInterface $catalogService)
    {
        $this->catalogService = $catalogService;
    }

    public function transform($value): ?CatalogInterface
    {
        if (null === $value) {
            return null;
        }

        if (!is_numeric($value)) {
            throw new TransformationFailedException("Invalid data, expected a integer'ish value");
        }

        try {
            return $this->catalogService->getCatalog((int) $value);
        } catch (NotFoundException | UnauthorizedException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function reverseTransform($value): ?int
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof CatalogInterface) {
            throw new TransformationFailedException('Expected a ' . CatalogInterface::class . ' object.');
        }

        return $value->getId();
    }
}
