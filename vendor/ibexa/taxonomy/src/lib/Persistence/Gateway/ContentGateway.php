<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Persistence\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Ibexa\Core\Persistence\Legacy\Content\Gateway;

final class ContentGateway implements ContentGatewayInterface
{
    private Connection $connection;

    public function __construct(
        Connection $connection
    ) {
        $this->connection = $connection;
    }

    /**
     * @return array<int>
     */
    public function findOrphanContentIds(int $taxonomyContentTypeId): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder
            ->select('c.id')
            ->from(Gateway::CONTENT_ITEM_TABLE, 'c')
            ->where('c.contentclass_id = :content_type_id')
            ->leftJoin('c', 'ibexa_taxonomy_entries', 't', 'c.id = t.content_id')
            ->andWhere('t.content_id IS NULL')
            ->setParameter('content_type_id', $taxonomyContentTypeId, Types::INTEGER);

        /** @var \Doctrine\DBAL\ForwardCompatibility\Result<array<array{id: int}>> $result */
        $result = $queryBuilder->execute();

        return array_map('intval', $result->fetchFirstColumn());
    }
}
