<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Content;

use Ibexa\Migration\ValueObject\Content\Field;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class MigrationFieldNormalizer implements DenormalizerInterface, NormalizerInterface, NormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use NormalizerAwareTrait;

    /**
     * @param \Ibexa\Migration\ValueObject\Content\Field $object
     * @param array<mixed> $context
     *
     * @return array{
     *     fieldDefIdentifier: string,
     *     languageCode: ?string,
     *     value: mixed,
     * }
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        return [
            'fieldDefIdentifier' => $object->fieldDefIdentifier,
            'languageCode' => $object->languageCode,
            'value' => $this->normalizer->normalize($object->value, $format, $context),
        ];
    }

    /**
     * @param array<string, mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Content\Field
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        return Field::createFromArray([
            'fieldDefIdentifier' => $data['fieldDefIdentifier'],
            'languageCode' => $this->prepareLanguageCode($data, $context),
            'value' => $data['value'],
        ]);
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $context
     */
    private function prepareLanguageCode(array $data, array $context = []): ?string
    {
        if (array_key_exists('languageCode', $data)
            && null !== $data['languageCode']) {
            return $data['languageCode'];
        }
        if (array_key_exists('mainLanguage', $context)) {
            return $context['mainLanguage'];
        }

        return null;
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Field;
    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return $type === Field::class;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}

class_alias(MigrationFieldNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Content\MigrationFieldNormalizer');
