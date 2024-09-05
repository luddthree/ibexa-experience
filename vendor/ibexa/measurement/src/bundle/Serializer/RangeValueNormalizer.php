<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Measurement\Serializer;

use Ibexa\Contracts\Measurement\MeasurementServiceInterface;
use Ibexa\Contracts\Measurement\Value\RangeValueInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class RangeValueNormalizer implements DenormalizerInterface, NormalizerInterface, CacheableSupportsMethodInterface
{
    private MeasurementServiceInterface $measurementService;

    public function __construct(
        MeasurementServiceInterface $measurementService
    ) {
        $this->measurementService = $measurementService;
    }

    public function denormalize($data, string $type, string $format = null, array $context = []): RangeValueInterface
    {
        Assert::isArray($data);
        Assert::keyExists($data, 'min_value');
        Assert::keyExists($data, 'max_value');
        Assert::keyExists($data, 'unit');

        Assert::keyExists($context, 'attribute_definition');
        $attributeDefinition = $context['attribute_definition'];
        Assert::isInstanceOf($attributeDefinition, AttributeDefinitionInterface::class);

        $options = $attributeDefinition->getOptions();
        Assert::true($options->has('type'));

        $typeName = $options->get('type');
        Assert::string($typeName);

        return $this->measurementService->buildRangeValue(
            $typeName,
            $data['min_value'],
            $data['max_value'],
            $data['unit'],
        );
    }

    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return is_a($type, RangeValueInterface::class, true);
    }

    /**
     * @param \Ibexa\Contracts\Measurement\Value\RangeValueInterface $object
     *
     * @return array{type: string, min_value: float, max_value: float, unit: string}
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        return [
            'type' => $object->getMeasurement()->getName(),
            'min_value' => $object->getMinValue(),
            'max_value' => $object->getMaxValue(),
            'unit' => $object->getUnit()->getIdentifier(),
        ];
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof RangeValueInterface;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
