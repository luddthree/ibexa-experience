<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Sql;

use InvalidArgumentException;

final class Query
{
    public const MYSQL = 'mysql';
    public const PGSQL = 'postgresql';
    public const SQLITE = 'sqlite';

    private const SUPPORTED_DRIVERS = [
        self::MYSQL => self::MYSQL,
        self::PGSQL => self::PGSQL,
        self::SQLITE => self::SQLITE,
    ];

    /** @var string */
    public $driver;

    /** @var string */
    public $sql;

    public function __construct(string $driver, string $sql)
    {
        $this->guardDriver($driver);

        $this->driver = $driver;
        $this->sql = $sql;
    }

    private function guardDriver(string $field): void
    {
        if (false === array_key_exists($field, self::SUPPORTED_DRIVERS)) {
            throw new InvalidArgumentException(sprintf(
                'Non supported driver name: %s. Supported fields: [%s]',
                $field,
                implode('|', self::SUPPORTED_DRIVERS)
            ));
        }
    }
}

class_alias(Query::class, 'Ibexa\Platform\Migration\ValueObject\Sql\Query');
