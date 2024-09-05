<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\Content;

use Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\UnhandledMatchPropertyException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Migration\ValueObject\Content\Matcher;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Webmozart\Assert\Assert;

final class ContentMatcherDenormalizer implements DenormalizerInterface, CacheableSupportsMethodInterface
{
    /**
     * @param mixed $data
     * @param array<mixed> $context
     *
     * @return array{field: string, value: scalar}
     */
    public function denormalize($data, string $type, string $format = null, array $context = []): array
    {
        Assert::isArray($data);

        if (isset($data['match']['location_id'])) {
            return [
                'field' => Matcher::LOCATION_ID,
                'value' => $data['match']['location_id'],
            ];
        }

        if (isset($data['match']['parent_location_id'])) {
            return [
                'field' => Matcher::PARENT_LOCATION_ID,
                'value' => $data['match']['parent_location_id'],
            ];
        }

        if (isset($data['match']['content_remote_id'])) {
            return [
                'field' => Matcher::CONTENT_REMOTE_ID,
                'value' => $data['match']['content_remote_id'],
            ];
        }

        if (isset($data['remote_id'])) {
            return [
                'field' => Matcher::CONTENT_REMOTE_ID,
                'value' => $data['remote_id'],
            ];
        }

        throw new UnhandledMatchPropertyException(
            array_keys($data['match']),
            [
                Matcher::LOCATION_ID,
                Matcher::PARENT_LOCATION_ID,
                Matcher::CONTENT_REMOTE_ID,
            ]
        );
    }

    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return $type === Criterion::class;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}

class_alias(ContentMatcherDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\Serializer\Content\ContentMatcherDenormalizer');
