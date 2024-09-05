<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Denormalizer;

use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer as BaseArrayDenormalizer;

/**
 * Wraps Symfony's ArrayDenormalizer and changes data passed into supportsDenormalization.
 *
 * Symfony's ArrayDenormalizer passes the whole array into $data, not one item. Since Action denormalizers rely both on
 * $data array containing "action" key and $type being ActionInterface, this effectively causes none of them to support
 * the array.
 *
 * @see https://github.com/symfony/symfony/issues/20837
 */
final class ArrayDenormalizer extends BaseArrayDenormalizer
{
    /**
     * @param mixed $data
     * @param array<string, mixed> $context
     */
    public function supportsDenormalization($data, string $type, string $format = null, array $context = []): bool
    {
        return parent::supportsDenormalization(null, $type, $format, $context);
    }
}

class_alias(ArrayDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Denormalizer\ArrayDenormalizer');
