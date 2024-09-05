<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Persistence;

final class BlockEntriesMapper implements EntriesMapperInterface
{
    /**
     * @return \Ibexa\FieldTypePage\Persistence\BlockEntry[]
     */
    public function map(array $rows): array
    {
        return array_reduce($rows, function (array $entries, $row): array {
            $entry = $this->mapEntry($row);

            if (null !== $entry) {
                $entries[] = $entry;
            }

            return $entries;
        }, []);
    }

    private function mapEntry(array $rawData): ?BlockEntry
    {
        if (!$this->isValid($rawData)) {
            return null;
        }

        return new BlockEntry(
            [
                'id' => (int)$rawData['id'],
                'userId' => (int)$rawData['user_id'],
                'contentId' => (int)$rawData['content_id'],
                'versionNumber' => (int)$rawData['version_number'],
                'actionTimestamp' => (int)$rawData['action_timestamp'],
                'blockName' => $rawData['block_name'],
                'blockType' => $rawData['block_type'],
            ]
        );
    }

    private function isValid(array $rawData): bool
    {
        return !empty($rawData)
            && array_key_exists('id', $rawData)
            && array_key_exists('user_id', $rawData)
            && array_key_exists('content_id', $rawData)
            && array_key_exists('version_number', $rawData)
            && array_key_exists('action_timestamp', $rawData)
            && array_key_exists('block_name', $rawData)
            && array_key_exists('block_type', $rawData);
    }
}

class_alias(BlockEntriesMapper::class, 'EzSystems\EzPlatformPageFieldType\Persistence\BlockEntriesMapper');
