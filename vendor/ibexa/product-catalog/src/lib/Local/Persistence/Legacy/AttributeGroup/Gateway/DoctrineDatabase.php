<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeGroup\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Types;
use Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadata;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadataRegistryInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\Core\Persistence\TransformationProcessor;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeGroup\Gateway\Translation\GatewayInterface as TranslationGateway;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeGroup\Gateway\Translation\Schema as TranslationSchema;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeGroup\GatewayInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeGroupCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeGroupUpdateStruct;

/**
 * @extends \Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase<array{
 *     id: int,
 *     identifier: string,
 *     position: int,
 * }>
 */
final class DoctrineDatabase extends AbstractDoctrineDatabase implements GatewayInterface
{
    private TranslationGateway $translationGateway;

    private TransformationProcessor $transformationProcessor;

    public function __construct(
        Connection $connection,
        DoctrineSchemaMetadataRegistryInterface $registry,
        TranslationGateway $translationGateway,
        TransformationProcessor $transformationProcessor
    ) {
        parent::__construct($connection, $registry);
        $this->translationGateway = $translationGateway;
        $this->transformationProcessor = $transformationProcessor;
    }

    protected function getTableName(): string
    {
        return Schema::TABLE_NAME;
    }

    protected function buildMetadata(): \Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadataInterface
    {
        $metadata = new DoctrineSchemaMetadata(
            $this->connection,
            AttributeGroupInterface::class,
            $this->getTableName(),
            [
                Schema::COLUMN_ID => Types::INTEGER,
                Schema::COLUMN_IDENTIFIER => Types::STRING,
                Schema::COLUMN_POSITION => Types::INTEGER,
            ],
            [Schema::COLUMN_ID],
        );

        $metadata->setTranslationSchemaMetadata($this->translationGateway->getMetadata());

        return $metadata;
    }

    public function findByIdentifier(string $identifier): ?array
    {
        return $this->findOneBy([
            Schema::COLUMN_IDENTIFIER => $identifier,
        ]);
    }

    public function findMatching(?string $namePrefix, int $offset, int $limit): array
    {
        $qb = $this->createBaseQueryBuilder('m');
        $qb->setFirstResult($offset);
        $qb->setMaxResults($limit);

        if ($namePrefix !== null) {
            $qb->where(
                $qb->expr()->in(
                    'm.' . Schema::COLUMN_ID,
                    $this->createNamePrefixSubQuery()->getSQL()
                )
            );

            $namePrefix = $this->getNormalizedName($namePrefix);
            $qb->setParameter(':name', addcslashes($namePrefix, '%_') . '%', ParameterType::STRING);
        }

        $qb->addOrderBy('m.' . Schema::COLUMN_POSITION, 'ASC');

        $results = $qb->execute()->fetchAllAssociative();

        return array_map(
            [$this->getMetadata(), 'convertToPHPValues'],
            $results,
        );
    }

    public function countMatching(?string $namePrefix): int
    {
        $qb = $this->createBaseQueryBuilder('m');
        $qb->select('COUNT(*)');

        if ($namePrefix !== null) {
            $qb->where(
                $qb->expr()->in(
                    'm.' . Schema::COLUMN_ID,
                    $this->createNamePrefixSubQuery()->getSQL()
                )
            );

            $namePrefix = $this->getNormalizedName($namePrefix);
            $qb->setParameter(':name', addcslashes($namePrefix, '%_') . '%', ParameterType::STRING);
        }

        return (int)$qb->execute()->fetchOne();
    }

    private function createNamePrefixSubQuery(string $alias = 't'): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select($alias . '.' . TranslationSchema::COLUMN_ATTRIBUTE_GROUP_ID);
        $qb->from(TranslationSchema::TABLE_NAME, $alias);
        $qb->where(
            $qb->expr()->like(
                $alias . '.' . TranslationSchema::COLUMN_NAME_NORMALIZED,
                ':name'
            )
        );

        return $qb;
    }

    public function insert(AttributeGroupCreateStruct $createStruct): int
    {
        $attributeGroupId = $this->doInsert([
            Schema::COLUMN_IDENTIFIER => $createStruct->identifier,
            Schema::COLUMN_POSITION => $createStruct->position,
        ]);

        foreach ($createStruct->names as $languageId => $name) {
            $this->translationGateway->insert($attributeGroupId, $languageId, [
                TranslationSchema::COLUMN_NAME => $name,
                TranslationSchema::COLUMN_NAME_NORMALIZED => $this->getNormalizedName($name),
            ]);
        }

        return $attributeGroupId;
    }

    public function deleteByIdentifier(string $identifier): void
    {
        $this->doDelete([
            Schema::COLUMN_IDENTIFIER => $identifier,
        ]);
    }

    public function update(AttributeGroupUpdateStruct $updateStruct): void
    {
        $data = [];
        if ($updateStruct->position !== null) {
            $data[Schema::COLUMN_POSITION] = $updateStruct->position;
        }

        if ($updateStruct->identifier !== null) {
            $data[Schema::COLUMN_IDENTIFIER] = $updateStruct->identifier;
        }

        if (!empty($data)) {
            $this->doUpdate([
                Schema::COLUMN_ID => $updateStruct->id,
            ], $data);
        }

        if ($updateStruct->names !== null) {
            foreach ($updateStruct->names as $languageId => $name) {
                $this->translationGateway->save($updateStruct->id, $languageId, [
                    TranslationSchema::COLUMN_NAME => $name,
                    TranslationSchema::COLUMN_NAME_NORMALIZED => $this->getNormalizedName($name),
                ]);
            }
        }
    }

    private function getNormalizedName(string $name): string
    {
        return $this->transformationProcessor->transformByGroup($name, 'lowercase');
    }
}
