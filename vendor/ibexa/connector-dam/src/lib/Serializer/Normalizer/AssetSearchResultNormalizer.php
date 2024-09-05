<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam\Serializer\Normalizer;

use Ibexa\Contracts\Connector\Dam\Search\AssetSearchResult;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

final class AssetSearchResultNormalizer implements ContextAwareNormalizerInterface
{
    /** @var \Symfony\Component\Serializer\Normalizer\ObjectNormalizer */
    private $normalizer;

    public function __construct(ObjectNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof AssetSearchResult;
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        $context[AbstractNormalizer::IGNORED_ATTRIBUTES][] = 'iterator';

        return $this->normalizer->normalize($object, $format, $context);
    }
}

class_alias(AssetSearchResultNormalizer::class, 'Ibexa\Platform\Connector\Dam\Serializer\Normalizer\AssetSearchResultNormalizer');
