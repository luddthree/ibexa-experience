<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Measurement\Serializer;

use Ibexa\Contracts\Measurement\MeasurementServiceInterface;
use Ibexa\Contracts\Measurement\Value\SimpleValueInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class SimpleValueNormalizer implements DenormalizerInterface, NormalizerInterface, CacheableSupportsMethodInterface
{
    private MeasurementServiceInterface $measurementService;

    public function __construct(
        MeasurementServiceInterface $measurementService
    ) {
        $this->measurementService = $measurementService;
    }

    public function denormalize($data, string $type, string $format = null, array $context = []): SimpleValueInterface
    {
        Assert::isArray($data);
        Assert::keyExists($data, 'value');
        Assert::keyExists($data, 'unit');

        Assert::keyExists($context, 'attribute_definition');
        $attributeDefinition = $context['attribute_definition'];
        Assert::isInstanceOf($attributeDefinition, AttributeDefinitionInterface::class);

        $options = $attributeDefinition->getOptions();
        Assert::true($options->has('type'));

        $typeName = $options->get('type');
        Assert::string($typeName);

        return $this->measurementService->buildSimpleValue(
            $typeName,
            $data['value'],
            $data['unit'],
        );
    }

    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return is_a($type, SimpleValueInterface::class, true);
    }

    /**
     * @param \Ibexa\Contracts\Measurement\Value\SimpleValueInterface $object
     *
     * @return array{type: string, value: float, unit: string}
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        return [
            'type' => $object->getMeasurement()->getName(),
            'value' => $object->getValue(),
            'unit' => $object->getUnit()->getIdentifier(),
        ];
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof SimpleValueInterface;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
