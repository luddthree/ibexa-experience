<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Persistence\Legacy\MigrateRichTextNamespaces\Gateway;

use Doctrine\DBAL\Connection;
use Ibexa\Contracts\FieldTypeRichText\Persistence\Legacy\MigrateRichTextNamespaces\AbstractGateway;

/**
 * @internal
 */
final class DoctrineDatabase extends AbstractGateway
{
    private const TABLE_EZPAGE_ATTRIBUTES = 'ezpage_attributes';
    private const COLUMN_VALUE = 'value';

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function migrate(array $values): int
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->update(self::TABLE_EZPAGE_ATTRIBUTES)
            ->set(
                self::COLUMN_VALUE,
                $this->addReplaceStatement($queryBuilder, self::COLUMN_VALUE, $values)
            );

        return (int)$queryBuilder->execute();
    }
}
