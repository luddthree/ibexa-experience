<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\ValueObject\Location\Matcher;
use RuntimeException;

final class LocationUpdateDenormalizer extends AbstractDenormalizer
{
    protected function getHandledKaliopType(): string
    {
        return 'location';
    }

    protected function getHandledKaliopMode(): string
    {
        return 'update';
    }

    protected function convertFromKaliopFormat(
        array $data,
        string $type,
        string $format = null,
        array $context = []
    ): array {
        return [
            'type' => 'location',
            'mode' => Mode::UPDATE,
            'match' => $this->prepareMatch($data),
            'metadata' => $this->prepareMetadata($data),
        ];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array{
     *   field: string,
     *   value: mixed,
     * }
     */
    private function prepareMatch(array $data): array
    {
        if (isset($data['match']['location_id'])) {
            return [
                'field' => Matcher::LOCATION_ID,
                'value' => $data['match']['location_id'],
            ];
        } elseif (isset($data['match']['location_remote_id'])) {
            return [
                'field' => Matcher::LOCATION_REMOTE_ID,
                'value' => $data['match']['location_remote_id'],
            ];
        } elseif (isset($data['location_remote_id'])) {
            return [
                'field' => Matcher::LOCATION_REMOTE_ID,
                'value' => $data['location_remote_id'],
            ];
        } elseif (isset($data['location_id'])) {
            return [
                'field' => Matcher::LOCATION_ID,
                'value' => $data['location_id'],
            ];
        }

        throw new UnhandledMatchPropertyException(
            array_keys($data['match']),
            [
                Matcher::LOCATION_ID,
                Matcher::LOCATION_REMOTE_ID,
            ]
        );
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array{
     *      remoteId: string,
     *      sortOrder: int,
     *      sortField: int,
     *      priority: int,
     * }
     */
    private function prepareMetadata(array $data): array
    {
        return [
            'remoteId' => $data['remote_id'] ?? null,
            'priority' => $data['priority'] ?? null,
            'sortField' => isset($data['sort_field']) ? $this->resolveSortField($data['sort_field']) : null,
            'sortOrder' => isset($data['sort_order']) ? $this->resolveSortOrder($data['sort_order']) : null,
        ];
    }

    private function resolveSortOrder(string $sortOrder): int
    {
        switch (strtoupper($sortOrder)) {
            case 'ASC':
                return Location::SORT_ORDER_ASC;
            case 'DESC':
                return Location::SORT_ORDER_DESC;
            default:
                throw new RuntimeException(
                    sprintf(
                        'No Supported SortOrder found (Given: %s)',
                        $sortOrder
                    )
                );
        }
    }

    private function resolveSortField(string $sortField): int
    {
        switch (strtoupper($sortField)) {
            case 'PATH':
                return Location::SORT_FIELD_PATH;
            case 'PUBLISHED':
                return Location::SORT_FIELD_PUBLISHED;
            case 'MODIFIED':
                return Location::SORT_FIELD_MODIFIED;
            case 'SECTION':
                return Location::SORT_FIELD_SECTION;
            case 'DEPTH':
                return Location::SORT_FIELD_DEPTH;
            case 'CLASS_IDENTIFIER':
                return Location::SORT_FIELD_CLASS_IDENTIFIER;
            case 'CLASS_NAME':
                return Location::SORT_FIELD_CLASS_NAME;
            case 'PRIORITY':
                return Location::SORT_FIELD_PRIORITY;
            case 'NAME':
                return Location::SORT_FIELD_NAME;
            case 'LOCATION_ID':
                return Location::SORT_FIELD_NODE_ID;
            case 'CONTENT_ID':
                return Location::SORT_FIELD_CONTENTOBJECT_ID;
            default:
                throw new RuntimeException(
                    sprintf(
                        'No Supported SortField found (Given: %s)',
                        $sortField
                    )
                );
        }
    }
}

class_alias(LocationUpdateDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\Serializer\LocationUpdateDenormalizer');
