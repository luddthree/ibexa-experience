<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\Reference;

use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Webmozart\Assert\Assert;

final class ReferenceArrayDenormalizer implements DenormalizerInterface, CacheableSupportsMethodInterface
{
    /**
     * @param array<string, mixed> $context
     *
     * @return array<array{
     *     name: string,
     *     type: string,
     * }>
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $references = [];

        foreach ($data as $possibleIdentifier => $datum) {
            if (is_string($possibleIdentifier) && is_string($datum)) {
                $reference = [
                    'name' => $possibleIdentifier,
                    'type' => $datum,
                ];
            } else {
                Assert::isArray($datum);
                Assert::keyExists($datum, 'identifier');
                Assert::keyExists($datum, 'attribute');
                $reference = [
                    'name' => $datum['identifier'],
                    'type' => $datum['attribute'],
                ];
            }

            $references[] = $reference;
        }

        return $references;
    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return $type === ReferenceDefinition::class . '[]';
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}

class_alias(ReferenceArrayDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\Serializer\Reference\ReferenceArrayDenormalizer');
