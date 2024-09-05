<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Attribute\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Types\Types;
use Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineRelationship;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadata;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadataRegistryInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Persistence\Gateway\StorageSchema as ProductStorageSchema;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Attribute\GatewayInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinition\Gateway\Schema as AttributeDefinitionSchema;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinition;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeUpdateStruct;
use InvalidArgumentException;

/**
 * @phpstan-import-type Data from \Ibexa\ProductCatalog\Local\Persistence\Legacy\Attribute\GatewayInterface
 *
 * @extends \Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase<Data>
 */
final class DoctrineDatabase extends AbstractDoctrineDatabase implements GatewayInterface
{
    /** @var iterable<\Ibexa\ProductCatalog\Local\Persistence\Legacy\Attribute\Inheritance\GatewayInterface<array<mixed>>> */
    private iterable $gateways;

    /**
     * @param iterable<\Ibexa\ProductCatalog\Local\Persistence\Legacy\Attribute\Inheritance\GatewayInterface<array<mixed>>> $subtypeGateways
     */
    public function __construct(
        Connection $connection,
        DoctrineSchemaMetadataRegistryInterface $registry,
        iterable $subtypeGateways
    ) {
        parent::__construct($connection, $registry);
        $this->gateways = $subtypeGateways;
    }

    protected function getTableName(): string
    {
        return StorageSchema::TABLE_NAME;
    }

    protected function buildMetadata(): DoctrineSchemaMetadata
    {
        $metadata = new DoctrineSchemaMetadata(
            $this->connection,
            AttributeInterface::class,
            $this->getTableName(),
            [
                StorageSchema::COLUMN_ID => Types::INTEGER,
                StorageSchema::COLUMN_PRODUCT_SPECIFICATION_ID => Types::INTEGER,
                StorageSchema::COLUMN_ATTRIBUTE_DEFINITION_ID => Types::INTEGER,
                StorageSchema::COLUMN_DISCRIMINATOR => Types::STRING,
            ],
            [StorageSchema::COLUMN_ID]
        );

        $metadata->addRelationship(new DoctrineRelationship(
            AttributeDefinitionInterface::class,
            'attribute_definition',
            StorageSchema::COLUMN_ATTRIBUTE_DEFINITION_ID,
            AttributeDefinitionSchema::COLUMN_ID,
        ));

        $metadata->addRelationship(new DoctrineRelationship(
            ProductInterface::class,
            'product',
            StorageSchema::COLUMN_PRODUCT_SPECIFICATION_ID,
            ProductStorageSchema::COLUMN_ID,
        ));

        foreach ($this->gateways as $gateway) {
            foreach ($gateway->getDiscriminators() as $discriminator) {
                $metadata->addSubclass($discriminator, $gateway->getMetadata());
            }
        }

        return $metadata;
    }

    public function findByProduct(int $productSpecificationId): array
    {
        return $this->findBy([
            StorageSchema::COLUMN_PRODUCT_SPECIFICATION_ID => $productSpecificationId,
        ], [
            StorageSchema::COLUMN_ATTRIBUTE_DEFINITION_ID => 'ASC',
        ]);
    }

    public function create(AttributeCreateStruct $struct, AttributeDefinition $attributeDefinition): int
    {
        $discriminator = $attributeDefinition->type;
        $productSpecificationId = $struct->getProductSpecificationId();
        $id = $this->doInsert([
            StorageSchema::COLUMN_PRODUCT_SPECIFICATION_ID => $productSpecificationId,
            StorageSchema::COLUMN_ATTRIBUTE_DEFINITION_ID => $attributeDefinition->id,
            StorageSchema::COLUMN_DISCRIMINATOR => $discriminator,
        ]);

        foreach ($this->gateways as $gateway) {
            if ($gateway->canHandle($discriminator)) {
                $gateway->create($discriminator, $id, $struct->getValue());

                return $id;
            }
        }

        throw new InvalidArgumentException(sprintf(
            'Unhandled Attribute of type "%s"',
            $discriminator,
        ));
    }

    public function update(AttributeUpdateStruct $updateStruct): void
    {
        foreach ($this->gateways as $gateway) {
            if ($gateway->canHandle($updateStruct->discriminator)) {
                $gateway->update($updateStruct->discriminator, $updateStruct->id, $updateStruct->value);

                return;
            }
        }

        throw new InvalidArgumentException(sprintf(
            'Unhandled Attribute of type "%s"',
            $updateStruct->discriminator,
        ));
    }

    public function deleteByProduct(int $productSpecificationId): void
    {
        $this->doDelete([
            StorageSchema::COLUMN_PRODUCT_SPECIFICATION_ID => $productSpecificationId,
        ]);
    }

    public function deleteByAttributeDefinition(int $attributeDefinitionId): void
    {
        $this->doDelete([
            StorageSchema::COLUMN_ATTRIBUTE_DEFINITION_ID => $attributeDefinitionId,
        ]);
    }

    public function countProductsContainingAttribute(int $id): int
    {
        return $this->countBy([StorageSchema::COLUMN_ATTRIBUTE_DEFINITION_ID => $id]);
    }

    public function countProductsContainingAttributeGroup(int $id): int
    {
        $qb = $this->connection->createQueryBuilder();

        $qb
            ->select(
                $this->connection->getDatabasePlatform()->getCountExpression('psa.' . StorageSchema::COLUMN_ID),
            )
            ->from(StorageSchema::TABLE_NAME, 'psa')
            ->join(
                'psa',
                AttributeDefinitionSchema::TABLE_NAME,
                'ad',
                $qb->expr()->eq(
                    'ad.' . AttributeDefinitionSchema::COLUMN_ID,
                    'psa.' . StorageSchema::COLUMN_ATTRIBUTE_DEFINITION_ID
                )
            )
            ->where(
                $qb->expr()->eq(
                    AttributeDefinitionSchema::COLUMN_ATTRIBUTE_GROUP_ID,
                    $qb->createNamedParameter($id, ParameterType::INTEGER)
                )
            );

        return (int)$qb->execute()->fetchOne();
    }
}
