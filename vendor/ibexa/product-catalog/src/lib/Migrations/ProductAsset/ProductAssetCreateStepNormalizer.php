<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\ProductAsset;

use Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Webmozart\Assert\Assert;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<\Ibexa\ProductCatalog\Migrations\ProductAsset\ProductAssetCreateStep>
 */
final class ProductAssetCreateStepNormalizer extends AbstractStepNormalizer
{
    /**
     * @param \Ibexa\ProductCatalog\Migrations\ProductAsset\ProductAssetCreateStep $object
     */
    protected function normalizeStep(
        StepInterface $object,
        string $format = null,
        array $context = []
    ): array {
        assert($object instanceof ProductAssetCreateStep);

        return [
            'product_code' => $object->getProductCode(),
            'uri' => $object->getUri(),
            'tags' => $object->getTags(),
        ];
    }

    protected function denormalizeStep(
        $data,
        string $type,
        string $format,
        array $context = []
    ): ProductAssetCreateStep {
        Assert::keyExists($data, 'product_code');
        Assert::stringNotEmpty($data['product_code']);
        Assert::keyExists($data, 'uri');
        Assert::stringNotEmpty($data['uri']);
        Assert::keyExists($data, 'tags');
        Assert::isArray($data['tags']);

        return new ProductAssetCreateStep(
            $data['product_code'],
            $data['uri'],
            $data['tags'],
        );
    }

    public function getHandledClassType(): string
    {
        return ProductAssetCreateStep::class;
    }

    public function getType(): string
    {
        return 'product_asset';
    }

    public function getMode(): string
    {
        return 'create';
    }
}
