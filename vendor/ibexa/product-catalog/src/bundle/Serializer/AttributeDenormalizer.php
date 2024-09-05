<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Serializer;

use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorResolverInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Webmozart\Assert\Assert;

/**
 * Attribute denormalizer is responsible for picking the right class type for an attribute object.
 *
 * It is heavily inspired by Symfony's ObjectNormalizer, where a similar ClassDiscriminatorResolver is used.
 *
 * @see \Symfony\Component\Serializer\Normalizer\ObjectNormalizer
 * @see \Symfony\Component\Serializer\Mapping\ClassDiscriminatorResolverInterface
 * @see \Symfony\Component\Serializer\Mapping\ClassDiscriminatorMapping
 */
final class AttributeDenormalizer implements DenormalizerInterface, CacheableSupportsMethodInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private ClassDiscriminatorResolverInterface $classDiscriminatorResolver;

    public function __construct(ClassDiscriminatorResolverInterface $classDiscriminatorResolver)
    {
        $this->classDiscriminatorResolver = $classDiscriminatorResolver;
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $mapping = $this->classDiscriminatorResolver->getMappingForClass($type);
        Assert::notNull($mapping, sprintf(
            'Mapping for class  %s does not exist.',
            $type,
        ));

        $typeProperty = $mapping->getTypeProperty();
        if (!isset($data[$typeProperty])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected type property %s to exist on data. Found properties: %s.',
                    $typeProperty,
                    implode(', ', array_keys($data)),
                ),
            );
        }

        $mappedType = $data[$typeProperty];
        $class = $mapping->getClassForType($mappedType);
        Assert::notNull($class, sprintf(
            'Unable to find class mapping for type %s for class %s.',
            $mappedType,
            $type,
        ));

        return $this->denormalizer->denormalize($data['value'] ?? null, $class, $format, $context);
    }

    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return $this->classDiscriminatorResolver->getMappingForClass($type) !== null;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
