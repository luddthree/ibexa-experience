<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\AssetServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Asset\AssetInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @template-implements \Symfony\Component\Form\DataTransformerInterface<
 *     \Ibexa\Contracts\ProductCatalog\Values\Asset\AssetInterface, string
 * >
 */
final class AssetTransformer implements DataTransformerInterface
{
    private AssetServiceInterface $assetService;

    private ProductInterface $product;

    public function __construct(AssetServiceInterface $assetService, ProductInterface $product)
    {
        $this->assetService = $assetService;
        $this->product = $product;
    }

    public function transform($value): ?string
    {
        if (null === $value) {
            return null;
        }

        if (!($value instanceof AssetInterface)) {
            throw new TransformationFailedException(
                sprintf(
                    'Expected a %s object, received %s.',
                    AssetInterface::class,
                    get_debug_type($value)
                )
            );
        }

        return $value->getIdentifier();
    }

    public function reverseTransform($value): ?AssetInterface
    {
        if (empty($value)) {
            return null;
        }

        if (!is_string($value)) {
            throw new TransformationFailedException('Invalid data, expected a string value');
        }

        try {
            return $this->assetService->getAsset($this->product, $value);
        } catch (NotFoundException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
