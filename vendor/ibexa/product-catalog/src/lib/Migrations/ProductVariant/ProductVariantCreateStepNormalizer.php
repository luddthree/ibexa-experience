<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\ProductVariant;

use Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Webmozart\Assert\Assert;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<\Ibexa\ProductCatalog\Migrations\ProductVariant\ProductVariantCreateStep>
 */
final class ProductVariantCreateStepNormalizer extends AbstractStepNormalizer
{
    /**
     * @param \Ibexa\ProductCatalog\Migrations\ProductVariant\ProductVariantCreateStep $object
     */
    protected function normalizeStep(
        StepInterface $object,
        string $format = null,
        array $context = []
    ): array {
        assert($object instanceof ProductVariantCreateStep);

        return [
            'base_product_code' => $object->getBaseProductCode(),
            'variants' => array_map(
                static fn (ProductVariantCreateStepEntry $entry): array => [
                    'code' => $entry->getCode(),
                    'attributes' => $entry->getAttributes(),
                ],
                $object->getVariants()
            ),
        ];
    }

    protected function denormalizeStep(
        $data,
        string $type,
        string $format,
        array $context = []
    ): ProductVariantCreateStep {
        Assert::keyExists($data, 'base_product_code');
        Assert::stringNotEmpty($data['base_product_code']);
        Assert::keyExists($data, 'variants');
        Assert::isArray($data['variants']);
        Assert::notEmpty($data['variants']);

        $entries = [];
        foreach ($data['variants'] as $variant) {
            Assert::nullOrStringNotEmpty($variant['code'] ?? null);
            Assert::keyExists($variant, 'attributes');
            Assert::isArray($variant['attributes']);
            Assert::notEmpty($variant['attributes']);

            $entries[] = new ProductVariantCreateStepEntry(
                $variant['attributes'],
                $variant['code'] ?? null
            );
        }

        return new ProductVariantCreateStep(
            $data['base_product_code'],
            $entries
        );
    }

    public function getHandledClassType(): string
    {
        return ProductVariantCreateStep::class;
    }

    public function getType(): string
    {
        return 'product_variant';
    }

    public function getMode(): string
    {
        return 'create';
    }
}
