<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\ContentType;

use Ibexa\Migration\ValueObject\ContentType\FieldDefinition;
use Ibexa\Migration\ValueObject\ContentType\FieldDefinitionCollection;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class FieldDefinitionCollectionNormalizer implements NormalizerInterface, NormalizerAwareInterface, DenormalizerInterface, DenormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use NormalizerAwareTrait;
    use DenormalizerAwareTrait;

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionCollection $object
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        $fields = [];

        /** @var \Ibexa\Migration\ValueObject\ContentType\FieldDefinition $fieldDefinition */
        foreach ($object as $fieldDefinition) {
            $fields[] = $this->normalizer->normalize($fieldDefinition, $format, $context);
        }

        return $fields;
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $fields = $this->denormalizer->denormalize(
            $data,
            FieldDefinition::class . '[]',
            $format,
            $context
        );

        return FieldDefinitionCollection::create($fields);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof FieldDefinitionCollection;
    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return $type === FieldDefinitionCollection::class;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}

class_alias(FieldDefinitionCollectionNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\ContentType\FieldDefinitionCollectionNormalizer');
