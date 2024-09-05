<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\ValueObject\Sql\Query;

final class SQLExecuteDenormalizer extends AbstractDenormalizer
{
    private const SUPPORTED_DRIVERS_MAPPING = [
        'mysql' => Query::MYSQL,
        'sqlite' => Query::SQLITE,
        'postgresql' => Query::PGSQL,
    ];

    protected function getHandledKaliopType(): string
    {
        return 'sql';
    }

    protected function getHandledKaliopMode(): string
    {
        return 'exec';
    }

    /**
     * @param array<mixed> $data
     */
    protected function supportsKaliopModeAndType(array $data): bool
    {
        return parent::supportsKaliopModeAndType($data)
            || ($data['type'] === $this->getHandledKaliopType() && !\array_key_exists('mode', $data));
    }

    /**
     * @param array<mixed> $data
     * @param array<mixed> $context
     *
     * @return array{
     *      mode: string,
     *      type: string,
     *      query: array<int, array{
     *          driver: string,
     *          sql: string
     *      }>,
     * }
     */
    protected function convertFromKaliopFormat(array $data, string $type, string $format = null, array $context = []): array
    {
        return array_merge(
            [
            'type' => 'sql',
            'mode' => Mode::EXECUTE,
        ],
            ['query' => $this->prepareQueries($data)]
        );
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<int, array{
     *      driver: string,
     *      sql: string
     * }> $translations
     */
    private function prepareQueries(array $data): array
    {
        $queries = [];

        foreach (self::SUPPORTED_DRIVERS_MAPPING as $kaliopName => $driverName) {
            if (\array_key_exists($kaliopName, $data)) {
                $queries[] = [
                    'driver' => $driverName,
                    'sql' => $data[$kaliopName],
                ];
            }
        }

        return $queries;
    }
}

class_alias(SQLExecuteDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\Serializer\SQLExecuteDenormalizer');
