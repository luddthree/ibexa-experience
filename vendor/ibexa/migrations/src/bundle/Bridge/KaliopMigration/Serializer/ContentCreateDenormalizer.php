<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

final class ContentCreateDenormalizer extends AbstractDenormalizer implements DenormalizerAwareInterface
{
    use ContentFieldPreparationTrait;
    use DenormalizerAwareTrait;

    protected function getHandledKaliopType(): string
    {
        return 'content';
    }

    protected function getHandledKaliopMode(): string
    {
        return 'create';
    }

    protected function convertFromKaliopFormat(array $data, string $type, string $format = null, array $context = []): array
    {
        $contentTypeIdentifier = $data['content_type'];
        $fields = $this->prepareFields($data);

        $convertedData = [
            'type' => $data['type'],
            'mode' => $data['mode'],
            'metadata' => [
                'contentType' => $contentTypeIdentifier,
                'mainTranslation' => $data['lang'] ?? null,
                'creatorId' => $data['owner'] ?? null,
                'modificationDate' => $this->convertTimestamp($data['modification_date'] ?? null),
                'publicationDate' => $this->convertTimestamp($data['publication_date'] ?? null),
                'remoteId' => $data['remote_id'] ?? null,
                'alwaysAvailable' => $data['always_available'] ?? null,
                'section' => $data['section'] ?? null,
            ],
            'location' => [
                'locationRemoteId' => $data['location_remote_id'] ?? null,
                'hidden' => $data['is_hidden'] ?? null,
                'sortField' => $data['sort_field'] ?? null,
                'sortOrder' => $data['sort_order'] ?? null,
                'priority' => $data['priority'] ?? null,
            ],
            'fields' => $fields,
        ];

        $references = $this->denormalizer->denormalize($data['references'] ?? [], ReferenceDefinition::class . '[]', $format, $context);
        if ($references) {
            $convertedData['references'] = $references;
        }

        self::setParentLocationOnConvertedData($data, $convertedData);

        return $convertedData;
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $convertedData
     */
    private static function setParentLocationOnConvertedData(array $data, array &$convertedData): void
    {
        if (is_numeric($data['parent_location'])) {
            $convertedData['location']['parentLocationId'] = $data['parent_location'];

            return;
        }

        if (is_string($data['parent_location'])) {
            $convertedData['location']['parentLocationRemoteId'] = $data['parent_location'];

            return;
        }

        $convertedData['location']['parentLocationId'] = null;
        $convertedData['location']['parentLocationRemoteId'] = null;
    }

    /**
     * Converts a timestamp into a string representing date.
     *
     * @param mixed|string|int|null $value
     *
     * @return mixed|string|null
     */
    private function convertTimestamp($value)
    {
        if (is_numeric($value) && $value == (int) $value) {
            return date(DATE_RFC3339, (int) $value);
        }

        return $value;
    }
}

class_alias(ContentCreateDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\Serializer\ContentCreateDenormalizer');
