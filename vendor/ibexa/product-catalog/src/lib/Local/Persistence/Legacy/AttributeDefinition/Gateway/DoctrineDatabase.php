<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinition\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineRelationship;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadata;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadataRegistryInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\Core\Persistence\TransformationProcessor;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinition\Gateway\Translation\GatewayInterface as TranslationGateway;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinition\Gateway\Translation\Schema as TranslationSchema;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinition\GatewayInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeGroup\Gateway\Schema as AttributeGroupSchema;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinitionCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinitionUpdateStruct;

/**
 * @extends AbstractDoctrineDatabase<array{
 *     id: int,
 *     identifier: string,
 *     type: string,
 *     attribute_group_id: int,
 *     position: int,
 *     options: ?array<string,mixed>,
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

    protected function buildMetadata(): DoctrineSchemaMetadata
    {
        $metadata = new DoctrineSchemaMetadata(
            $this->connection,
            AttributeDefinitionInterface::class,
            $this->getTableName(),
            [
                Schema::COLUMN_ID => Types::INTEGER,
                Schema::COLUMN_IDENTIFIER => Types::STRING,
                Schema::COLUMN_TYPE => Types::STRING,
                Schema::COLUMN_ATTRIBUTE_GROUP_ID => Types::INTEGER,
                Schema::COLUMN_POSITION => Types::INTEGER,
                Schema::COLUMN_OPTIONS => Types::JSON,
            ],
            [Schema::COLUMN_ID],
        );

        $translationMetadata = $this->translationGateway->getMetadata();
        $metadata->setTranslationSchemaMetadata($translationMetadata);

        $metadata->addRelationship(new DoctrineRelationship(
            AttributeGroupInterface::class,
            'attribute_group',
            Schema::COLUMN_ATTRIBUTE_GROUP_ID,
            AttributeGroupSchema::COLUMN_ID,
        ));

        return $metadata;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function findByIdentifier(string $identifier): ?array
    {
        return $this->findOneBy([
            Schema::COLUMN_IDENTIFIER => $identifier,
        ]);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function insert(AttributeDefinitionCreateStruct $createStruct): int
    {
        $attributeDefinitionId = $this->doInsert([
            Schema::COLUMN_IDENTIFIER => $createStruct->identifier,
            Schema::COLUMN_ATTRIBUTE_GROUP_ID => $createStruct->attributeGroupId,
            Schema::COLUMN_TYPE => $createStruct->type,
            Schema::COLUMN_POSITION => $createStruct->position,
            Schema::COLUMN_OPTIONS => $createStruct->options,
        ]);

        foreach ($createStruct->names as $languageId => $name) {
            $this->translationGateway->insert(
                $attributeDefinitionId,
                $languageId,
                [
                    TranslationSchema::COLUMN_NAME => $name,
                    TranslationSchema::COLUMN_NAME_NORMALIZED => $this->getNormalizedName($name),
                    TranslationSchema::COLUMN_DESCRIPTION => $createStruct->descriptions[$languageId] ?? '',
                ]
            );
        }

        return $attributeDefinitionId;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function deleteByIdentifier(string $identifier): void
    {
        $this->doDelete([
            Schema::COLUMN_IDENTIFIER => $identifier,
        ]);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function update(AttributeDefinitionUpdateStruct $updateStruct): void
    {
        $data = [
            Schema::COLUMN_IDENTIFIER => $updateStruct->identifier,
            Schema::COLUMN_ATTRIBUTE_GROUP_ID => $updateStruct->attributeGroupId,
            Schema::COLUMN_TYPE => $updateStruct->type,
            Schema::COLUMN_POSITION => $updateStruct->position,
            Schema::COLUMN_OPTIONS => $updateStruct->options,
        ];

        $data = array_filter($data);

        if (!empty($data)) {
            $this->doUpdate([
                Schema::COLUMN_ID => $updateStruct->id,
            ], $data);
        }

        foreach ($updateStruct->names as $languageId => $name) {
            $this->translationGateway->save(
                $updateStruct->id,
                $languageId,
                [
                    TranslationSchema::COLUMN_NAME => $name,
                    TranslationSchema::COLUMN_NAME_NORMALIZED => $this->getNormalizedName($name),
                    TranslationSchema::COLUMN_DESCRIPTION => $updateStruct->descriptions[$languageId] ?? '',
                ]
            );
        }
    }

    private function getNormalizedName(string $name): string
    {
        return $this->transformationProcessor->transformByGroup($name, 'lowercase');
    }
}
