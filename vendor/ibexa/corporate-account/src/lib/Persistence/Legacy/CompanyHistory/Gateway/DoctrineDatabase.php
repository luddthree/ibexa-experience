<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Persistence\Legacy\CompanyHistory\Gateway;

use Doctrine\DBAL\Types\Types;
use Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadata;
use Ibexa\Contracts\CorporateAccount\Values\CompanyHistory;
use Ibexa\CorporateAccount\Persistence\Legacy\CompanyHistory\GatewayInterface;
use Ibexa\CorporateAccount\Persistence\Values\CompanyHistoryCreateStruct;
use Ibexa\CorporateAccount\Persistence\Values\CompanyHistoryUpdateStruct;
use LogicException;

/**
 * @phpstan-import-type Data from \Ibexa\CorporateAccount\Persistence\Legacy\CompanyHistory\GatewayInterface
 *
 * @extends \Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase<Data>
 */
final class DoctrineDatabase extends AbstractDoctrineDatabase implements GatewayInterface
{
    protected function getTableName(): string
    {
        return StorageSchema::TABLE_NAME;
    }

    protected function buildMetadata(): DoctrineSchemaMetadata
    {
        $types = [
            StorageSchema::COLUMN_ID => Types::INTEGER,
            StorageSchema::COLUMN_APPLICATION_ID => Types::INTEGER,
            StorageSchema::COLUMN_COMPANY_ID => Types::INTEGER,
            StorageSchema::COLUMN_USER_ID => Types::INTEGER,
            StorageSchema::COLUMN_EVENT_NAME => Types::STRING,
            StorageSchema::COLUMN_EVENT_DATA => Types::JSON,
            StorageSchema::COLUMN_CREATED => Types::DATETIME_IMMUTABLE,
        ];
        $ids = [StorageSchema::COLUMN_ID];

        return new DoctrineSchemaMetadata(
            $this->connection,
            CompanyHistory::class,
            $this->getTableName(),
            $types,
            $ids,
        );
    }

    /**
     * @throws \Ibexa\Contracts\CorePersistence\Exception\MappingException
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function create(CompanyHistoryCreateStruct $struct): array
    {
        $id = $this->doInsert([
            StorageSchema::COLUMN_APPLICATION_ID => $struct->applicationId,
            StorageSchema::COLUMN_COMPANY_ID => $struct->companyId,
            StorageSchema::COLUMN_USER_ID => $struct->userId,
            StorageSchema::COLUMN_EVENT_NAME => $struct->eventName,
            StorageSchema::COLUMN_EVENT_DATA => $struct->eventData,
        ]);

        $companyHistory = $this->findById($id);
        if ($companyHistory === null) {
            throw new LogicException(sprintf(
                'Unable to find object with ID %d after persisting it in database.',
                $id,
            ));
        }

        return $companyHistory;
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
     * @throws \Ibexa\Contracts\CorePersistence\Exception\MappingException
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function update(CompanyHistoryUpdateStruct $struct): array
    {
        $data = [
            StorageSchema::COLUMN_APPLICATION_ID => $struct->applicationId,
            StorageSchema::COLUMN_COMPANY_ID => $struct->companyId,
            StorageSchema::COLUMN_USER_ID => $struct->userId,
            StorageSchema::COLUMN_EVENT_NAME => $struct->eventName,
            StorageSchema::COLUMN_EVENT_DATA => $struct->eventData,
        ];

        $criteria = [
            StorageSchema::COLUMN_ID => $struct->id,
        ];

        $this->doUpdate(
            $criteria,
            array_filter($data)
        );

        /**
         * @phpstan-var Data
         */
        return $this->findById($struct->id);
    }
}
