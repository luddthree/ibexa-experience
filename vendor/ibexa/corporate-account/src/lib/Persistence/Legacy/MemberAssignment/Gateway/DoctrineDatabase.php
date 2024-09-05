<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Persistence\Legacy\MemberAssignment\Gateway;

use Doctrine\DBAL\Types\Types;
use Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadata;
use Ibexa\Contracts\CorporateAccount\Values\MemberAssignment;
use Ibexa\CorporateAccount\Persistence\Legacy\MemberAssignment\GatewayInterface;
use Ibexa\CorporateAccount\Persistence\Values\MemberAssignmentCreateStruct;
use Ibexa\CorporateAccount\Persistence\Values\MemberAssignmentUpdateStruct;
use LogicException;

/**
 * @phpstan-import-type Data from \Ibexa\CorporateAccount\Persistence\Legacy\MemberAssignment\GatewayInterface
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
            StorageSchema::COLUMN_MEMBER_ID => Types::INTEGER,
            StorageSchema::COLUMN_MEMBER_ROLE => Types::STRING,
            StorageSchema::COLUMN_MEMBER_ROLE_ASSIGNMENT_ID => Types::INTEGER,
            StorageSchema::COLUMN_COMPANY_ID => Types::INTEGER,
            StorageSchema::COLUMN_COMPANY_LOCATION_ID => Types::INTEGER,
        ];
        $ids = [StorageSchema::COLUMN_ID];

        return new DoctrineSchemaMetadata(
            $this->connection,
            MemberAssignment::class,
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
    public function create(MemberAssignmentCreateStruct $struct): array
    {
        $id = $this->doInsert([
            StorageSchema::COLUMN_MEMBER_ID => $struct->memberId,
            StorageSchema::COLUMN_MEMBER_ROLE => $struct->memberRole,
            StorageSchema::COLUMN_MEMBER_ROLE_ASSIGNMENT_ID => $struct->memberRoleAssignmentId,
            StorageSchema::COLUMN_COMPANY_ID => $struct->companyId,
            StorageSchema::COLUMN_COMPANY_LOCATION_ID => $struct->companyLocationId,
        ]);

        $memberAssignment = $this->findById($id);
        if ($memberAssignment === null) {
            throw new LogicException(sprintf(
                'Unable to find object with ID %d after persisting it in database.',
                $id,
            ));
        }

        return $memberAssignment;
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
    public function update(MemberAssignmentUpdateStruct $struct): array
    {
        $data = [
            StorageSchema::COLUMN_MEMBER_ID => $struct->memberId,
            StorageSchema::COLUMN_MEMBER_ROLE => $struct->memberRole,
            StorageSchema::COLUMN_MEMBER_ROLE_ASSIGNMENT_ID => $struct->memberRoleAssignmentId,
            StorageSchema::COLUMN_COMPANY_ID => $struct->companyId,
            StorageSchema::COLUMN_COMPANY_LOCATION_ID => $struct->companyLocationId,
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
