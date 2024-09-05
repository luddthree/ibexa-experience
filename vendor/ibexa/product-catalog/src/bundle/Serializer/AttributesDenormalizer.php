<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Serializer;

use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Webmozart\Assert\Assert;

final class AttributesDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use DenormalizerAwareTrait;

    private AttributeDefinitionServiceInterface $attributeDefinitionService;

    public function __construct(
        AttributeDefinitionServiceInterface $attributeDefinitionService
    ) {
        $this->attributeDefinitionService = $attributeDefinitionService;
    }

    /**
     * @return array<scalar|object|null>
     */
    public function denormalize($data, string $type, string $format = null, array $context = []): array
    {
        Assert::isIterable($data);

        Assert::keyExists($context, 'product_type');
        /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface $productType */
        $productType = $context['product_type'];
        Assert::isInstanceOf($productType, ProductTypeInterface::class);

        $attributes = [];
        foreach ($data as $attributeIdentifier => $attributeValue) {
            $attributeDefinition = $this->attributeDefinitionService->getAttributeDefinition($attributeIdentifier);
            $attributeTypeIdentifier = $attributeDefinition->getType()->getIdentifier();

            $attributeValue = [
                'type' => $attributeTypeIdentifier,
                'value' => $attributeValue,
            ];

            $attributes[$attributeIdentifier] = $this->denormalizer->denormalize(
                $attributeValue,
                AttributeInterface::class,
                $format,
                [
                    'attribute_definition' => $attributeDefinition,
                ] + $context,
            );
        }

        return $attributes;
    }

    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return $type === AttributeInterface::class . '[]';
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
