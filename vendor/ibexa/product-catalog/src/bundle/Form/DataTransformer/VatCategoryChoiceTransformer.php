<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\Values\RegionInterface;
use Ibexa\Contracts\ProductCatalog\Values\VatCategoryInterface;
use Ibexa\Contracts\ProductCatalog\VatServiceInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements \Symfony\Component\Form\DataTransformerInterface<
 *     \Ibexa\Contracts\ProductCatalog\Values\VatCategoryInterface|string|int,
 *     \Ibexa\Contracts\ProductCatalog\Values\VatCategoryInterface|string|int,
 * >
 */
final class VatCategoryChoiceTransformer implements DataTransformerInterface
{
    private VatServiceInterface $vatService;

    private RegionInterface $region;

    public function __construct(
        VatServiceInterface $vatService,
        RegionInterface $region
    ) {
        $this->vatService = $vatService;
        $this->region = $region;
    }

    public function transform($value): ?VatCategoryInterface
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof VatCategoryInterface) {
            return $value;
        }

        try {
            return $this->vatService->getVatCategoryByIdentifier(
                $this->region,
                (string)$value
            );
        } catch (NotFoundException $e) {
            throw new TransformationFailedException(sprintf(
                'Unable to find %s in region "%s" with identifier "%s"',
                VatCategoryInterface::class,
                $this->region->getIdentifier(),
                $value,
            ), $e->getCode(), $e);
        }
    }

    public function reverseTransform($value): ?string
    {
        if ($value instanceof VatCategoryInterface) {
            return $value->getIdentifier();
        }

        return $value === null ? null : (string) $value;
    }
}
