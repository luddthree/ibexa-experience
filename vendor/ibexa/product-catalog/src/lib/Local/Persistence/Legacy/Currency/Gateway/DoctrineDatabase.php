<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Currency\Gateway;

use Doctrine\DBAL\Types\Types;
use Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadata;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Currency\GatewayInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\CurrencyCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\CurrencyUpdateStruct;
use LogicException;

/**
 * @phpstan-type Data array{
 *     id: int,
 *     code: non-empty-string,
 *     subunits: int<0, max>,
 *     enabled: bool,
 * }
 *
 * @extends \Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase<Data>
 */
final class DoctrineDatabase extends AbstractDoctrineDatabase implements GatewayInterface
{
    protected function getTableName(): string
    {
        return StorageSchema::TABLE_NAME;
    }

    protected function getTableAlias(): string
    {
        return 'i_c';
    }

    protected function buildMetadata(): DoctrineSchemaMetadata
    {
        $types = [
            StorageSchema::COLUMN_ID => Types::INTEGER,
            StorageSchema::COLUMN_CODE => Types::STRING,
            StorageSchema::COLUMN_SUBUNITS => Types::SMALLINT,
            StorageSchema::COLUMN_ENABLED => Types::BOOLEAN,
        ];
        $ids = [StorageSchema::COLUMN_ID];

        return new DoctrineSchemaMetadata(
            $this->connection,
            CurrencyInterface::class,
            $this->getTableName(),
            $types,
            $ids,
        );
    }

    /**
     * @throws \Ibexa\ProductCatalog\Local\Persistence\Legacy\Exception\MappingException
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function create(CurrencyCreateStruct $struct): array
    {
        $id = $this->doInsert([
            StorageSchema::COLUMN_CODE => $struct->code,
            StorageSchema::COLUMN_SUBUNITS => $struct->subunits,
            StorageSchema::COLUMN_ENABLED => $struct->enabled,
        ]);

        $currency = $this->findById($id);
        if ($currency === null) {
            throw new LogicException(sprintf(
                'Unable to find object with ID %d after persisting it in database.',
                $id,
            ));
        }

        return $currency;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function delete(int $id): void
    {
        $this->doDelete([
            StorageSchema::COLUMN_ID => $id,
        ]);
    }

    /**
     * @throws \Ibexa\ProductCatalog\Local\Persistence\Legacy\Exception\MappingException
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function update(CurrencyUpdateStruct $struct): array
    {
        $data = [
            StorageSchema::COLUMN_CODE => $struct->code,
            StorageSchema::COLUMN_SUBUNITS => $struct->subunits,
            StorageSchema::COLUMN_ENABLED => $struct->enabled,
        ];

        $criteria = [
            StorageSchema::COLUMN_ID => $struct->id,
        ];

        $this->doUpdate($criteria, $data);

        /**
         * @phpstan-var Data
         */
        return $this->findById($struct->id);
    }
}
