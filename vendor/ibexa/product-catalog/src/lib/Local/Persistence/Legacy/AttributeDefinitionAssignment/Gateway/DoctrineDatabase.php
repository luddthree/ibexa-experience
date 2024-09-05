<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinitionAssignment\Gateway;

use Doctrine\DBAL\Types\Types;
use Ibexa\Contracts\Core\Persistence\Content\Type;
use Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadata;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionAssignmentInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinition\Gateway\Schema as AttributeDefinitionSchema;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinitionAssignment\GatewayInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinitionAssignmentCreateStruct;

/**
 * @extends \Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase<array{
 *      id: int,
 *      field_definition_id: int,
 *      attribute_definition_id: int,
 *      required: bool,
 *      discriminator: bool,
 * }>
 */
final class DoctrineDatabase extends AbstractDoctrineDatabase implements GatewayInterface
{
    public const TABLE_NAME = 'ibexa_attribute_definition_assignment';

    public const COLUMN_ID = 'id';
    public const COLUMN_FIELD_DEFINITION_ID = 'field_definition_id';
    public const COLUMN_STATUS = 'status';
    public const COLUMN_ATTRIBUTE_DEFINITION_ID = 'attribute_definition_id';
    public const COLUMN_REQUIRED = 'required';
    public const COLUMN_DISCRIMINATOR = 'discriminator';

    protected function getTableName(): string
    {
        return self::TABLE_NAME;
    }

    protected function buildMetadata(): DoctrineSchemaMetadata
    {
        return new DoctrineSchemaMetadata(
            $this->connection,
            AttributeDefinitionAssignmentInterface::class,
            $this->getTableName(),
            [
                self::COLUMN_ID => Types::INTEGER,
                self::COLUMN_FIELD_DEFINITION_ID => Types::INTEGER,
                self::COLUMN_STATUS => Types::INTEGER,
                self::COLUMN_ATTRIBUTE_DEFINITION_ID => Types::INTEGER,
                self::COLUMN_REQUIRED => Types::BOOLEAN,
                self::COLUMN_DISCRIMINATOR => Types::BOOLEAN,
            ],
            [self::COLUMN_ID]
        );
    }

    public function findByFieldDefinitionId(int $fieldDefinitionId, int $status): array
    {
        return $this->findBy([
            self::COLUMN_FIELD_DEFINITION_ID => $fieldDefinitionId,
            self::COLUMN_STATUS => $status,
        ]);
    }

    public function insert(AttributeDefinitionAssignmentCreateStruct $createStruct, int $status): int
    {
        return $this->doInsert([
            self::COLUMN_FIELD_DEFINITION_ID => $createStruct->fieldDefinitionId,
            self::COLUMN_STATUS => $status,
            self::COLUMN_ATTRIBUTE_DEFINITION_ID => $createStruct->attributeDefinitionId,
            self::COLUMN_REQUIRED => $createStruct->required,
            self::COLUMN_DISCRIMINATOR => $createStruct->discriminator,
        ]);
    }

    public function publish(int $fieldDefinitionId): void
    {
        $this->doDelete([
            self::COLUMN_FIELD_DEFINITION_ID => $fieldDefinitionId,
            self::COLUMN_STATUS => Type::STATUS_DEFINED,
        ]);

        $this->doUpdate(
            [
                self::COLUMN_FIELD_DEFINITION_ID => $fieldDefinitionId,
                self::COLUMN_STATUS => Type::STATUS_DRAFT,
            ],
            [
                self::COLUMN_STATUS => Type::STATUS_DEFINED,
            ]
        );
    }

    public function deleteByFieldDefinitionId(int $fieldDefinitionId, int $status): void
    {
        $this->doDelete([
            self::COLUMN_FIELD_DEFINITION_ID => $fieldDefinitionId,
            self::COLUMN_STATUS => $status,
        ]);
    }

    public function getIdentityMap(int $fieldDefinitionId, bool $discriminator): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select(
            'definition.' . AttributeDefinitionSchema::COLUMN_ID,
            'definition.' . AttributeDefinitionSchema::COLUMN_IDENTIFIER,
        );
        $qb->from(AttributeDefinitionSchema::TABLE_NAME, 'definition');
        $qb->join(
            'definition',
            self::TABLE_NAME,
            'assignment',
            $qb->expr()->eq(
                'assignment.' . self::COLUMN_ATTRIBUTE_DEFINITION_ID,
                'definition.' . AttributeDefinitionSchema::COLUMN_ID
            )
        );
        $qb->where(
            $qb->expr()->eq(
                self::COLUMN_FIELD_DEFINITION_ID,
                $qb->createNamedParameter(
                    $fieldDefinitionId,
                    $this->getMetadata()->getBindingTypeForColumn(self::COLUMN_FIELD_DEFINITION_ID)
                )
            ),
            $qb->expr()->eq(
                self::COLUMN_DISCRIMINATOR,
                $qb->createNamedParameter(
                    $discriminator,
                    $this->getMetadata()->getBindingTypeForColumn(self::COLUMN_DISCRIMINATOR)
                )
            )
        );

        /**
         * @var array<int,string>
         */
        return $qb->execute()->fetchAllKeyValue();
    }
}
