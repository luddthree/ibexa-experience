<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Denormalizer\User;

use function array_merge;
use Ibexa\Migration\ValueObject\User\CreateMetadata;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CreateMetadataDenormalizer implements DenormalizerInterface, CacheableSupportsMethodInterface
{
    /** @var string */
    private $defaultLanguage;

    public function __construct(string $defaultLanguage)
    {
        $this->defaultLanguage = $defaultLanguage;
    }

    /**
     * @param array{
     *     login: string,
     *     email: string,
     *     enabled?: bool,
     *     password: string,
     *     mainLanguage?: string,
     *     contentType?: ?string,
     * } $data
     * @param array<mixed> $context
     */
    public function denormalize($data, string $type, string $format = null, array $context = []): CreateMetadata
    {
        $data = array_merge([
            'mainLanguage' => $this->defaultLanguage,
        ], $data);

        return CreateMetadata::createFromArray($data);
    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return $type === CreateMetadata::class;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}

class_alias(CreateMetadataDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Denormalizer\User\CreateMetadataDenormalizer');
