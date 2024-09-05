<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\ProductAvailability;

use Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Webmozart\Assert\Assert;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<\Ibexa\ProductCatalog\Migrations\ProductAvailability\ProductAvailabilityCreateStep>
 */
final class ProductAvailabilityCreateStepNormalizer extends AbstractStepNormalizer
{
    protected function normalizeStep(
        StepInterface $object,
        string $format = null,
        array $context = []
    ): array {
        assert($object instanceof ProductAvailabilityCreateStep);

        return [
            'product_code' => $object->getProductCode(),
            'stock' => $object->getStock(),
            'is_available' => $object->isAvailable(),
            'is_infinite' => $object->isInfinite(),
        ];
    }

    protected function denormalizeStep(
        $data,
        string $type,
        string $format,
        array $context = []
    ): ProductAvailabilityCreateStep {
        Assert::keyExists($data, 'product_code');
        Assert::stringNotEmpty($data['product_code']);
        Assert::keyExists($data, 'is_available');
        Assert::keyExists($data, 'is_infinite');

        return new ProductAvailabilityCreateStep(
            $data['product_code'],
            $data['stock'],
            $data['is_available'] ?? false,
            $data['is_infinite'] ?? false
        );
    }

    public function getHandledClassType(): string
    {
        return ProductAvailabilityCreateStep::class;
    }

    public function getType(): string
    {
        return 'product_availability';
    }

    public function getMode(): string
    {
        return 'create';
    }
}
