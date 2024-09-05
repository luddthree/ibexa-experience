<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Persistence\Legacy\ApplicationState\Gateway;

use Doctrine\DBAL\Types\Types;
use Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadata;
use Ibexa\Contracts\CorporateAccount\Values\ApplicationState;
use Ibexa\CorporateAccount\Persistence\Legacy\ApplicationState\GatewayInterface;
use Ibexa\CorporateAccount\Persistence\Values\ApplicationStateCreateStruct;
use Ibexa\CorporateAccount\Persistence\Values\ApplicationStateUpdateStruct;
use LogicException;

/**
 * @phpstan-import-type Data from \Ibexa\CorporateAccount\Persistence\Legacy\ApplicationState\GatewayInterface
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
            StorageSchema::COLUMN_STATE => Types::STRING,
            StorageSchema::COLUMN_COMPANY_ID => Types::INTEGER,
        ];
        $ids = [StorageSchema::COLUMN_ID];

        return new DoctrineSchemaMetadata(
            $this->connection,
            ApplicationState::class,
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
    public function create(ApplicationStateCreateStruct $struct): array
    {
        $id = $this->doInsert([
            StorageSchema::COLUMN_APPLICATION_ID => $struct->applicationId,
            StorageSchema::COLUMN_STATE => $struct->state,
            StorageSchema::COLUMN_COMPANY_ID => $struct->companyId,
        ]);

        $applicationState = $this->findById($id);
        if ($applicationState === null) {
            throw new LogicException(sprintf(
                'Unable to find object with ID %d after persisting it in database.',
                $id,
            ));
        }

        return $applicationState;
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
    public function update(ApplicationStateUpdateStruct $struct): array
    {
        $data = [
            StorageSchema::COLUMN_APPLICATION_ID => $struct->applicationId,
            StorageSchema::COLUMN_STATE => $struct->state,
            StorageSchema::COLUMN_COMPANY_ID => $struct->companyId,
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
