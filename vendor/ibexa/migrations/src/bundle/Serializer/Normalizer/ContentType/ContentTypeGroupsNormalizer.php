<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\ContentType;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ContentTypeGroupsNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup $object */
        if (isset($context['use_content_group_identifier']) && $context['use_content_group_identifier'] == true) {
            return $object->identifier;
        }

        return $object->id;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof ContentTypeGroup;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}

class_alias(ContentTypeGroupsNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\ContentType\ContentTypeGroupsNormalizer');
