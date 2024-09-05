<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Catalog\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadata;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadataRegistryInterface;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use Ibexa\Core\Persistence\TransformationProcessor;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Catalog\Gateway\Translation\GatewayInterface as TranslationGateway;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Catalog\Gateway\Translation\Schema as TranslationSchema;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Catalog\GatewayInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\CatalogCopyStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\CatalogCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\CatalogUpdateStruct;

/**
 * @extends \Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase<array{
 *     id: int,
 *     identifier: string,
 *     creator_id: int,
 *     created: int,
 *     modified: int,
 *     status: string,
 *     query_string: string,
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
        return StorageSchema::TABLE_NAME;
    }

    protected function getTableAlias(): string
    {
        return 'ic';
    }

    protected function buildMetadata(): DoctrineSchemaMetadata
    {
        $columnToTypesMap = [
            StorageSchema::COLUMN_ID => Types::INTEGER,
            StorageSchema::COLUMN_IDENTIFIER => Types::STRING,
            StorageSchema::COLUMN_CREATOR_ID => Types::INTEGER,
            StorageSchema::COLUMN_CREATED => Types::INTEGER,
            StorageSchema::COLUMN_MODIFIED => Types::INTEGER,
            StorageSchema::COLUMN_STATUS => Types::STRING,
            StorageSchema::COLUMN_QUERY_STRING => Types::STRING,
        ];
        $identifierColumns = [StorageSchema::COLUMN_ID];

        $translationMetadata = $this->translationGateway->getMetadata();

        $metadata = new DoctrineSchemaMetadata(
            $this->connection,
            CatalogInterface::class,
            $this->getTableName(),
            $columnToTypesMap,
            $identifierColumns,
        );

        $metadata->setTranslationSchemaMetadata($translationMetadata);

        return $metadata;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function insert(CatalogCreateStruct $createStruct): int
    {
        $id = $this->doInsert([
            StorageSchema::COLUMN_IDENTIFIER => $createStruct->identifier,
            StorageSchema::COLUMN_CREATOR_ID => $createStruct->creatorId,
            StorageSchema::COLUMN_CREATED => time(),
            StorageSchema::COLUMN_MODIFIED => time(),
            StorageSchema::COLUMN_STATUS => $createStruct->status,
            StorageSchema::COLUMN_QUERY_STRING => $createStruct->query,
        ]);

        foreach ($createStruct->names as $languageId => $name) {
            $this->translationGateway->insert(
                $id,
                $languageId,
                [
                    TranslationSchema::COLUMN_NAME => $name,
                    TranslationSchema::COLUMN_NAME_NORMALIZED => $this->getNormalizedName($name),
                    TranslationSchema::COLUMN_DESCRIPTION => $createStruct->descriptions[$languageId],
                ]
            );
        }

        return $id;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function update(CatalogUpdateStruct $updateStruct): void
    {
        $data = [
            StorageSchema::COLUMN_IDENTIFIER => $updateStruct->identifier,
            StorageSchema::COLUMN_MODIFIED => time(),
            StorageSchema::COLUMN_STATUS => $updateStruct->status,
            StorageSchema::COLUMN_QUERY_STRING => $updateStruct->query,
        ];
        $data = array_filter($data);

        $criteria = [
            StorageSchema::COLUMN_ID => $updateStruct->id,
        ];

        $this->doUpdate($criteria, $data);

        if ($updateStruct->names !== null) {
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

    public function findById(int $id): ?array
    {
        return $this->findOneBy([
            StorageSchema::COLUMN_ID => $id,
        ]);
    }

    private function getNormalizedName(?string $name): ?string
    {
        if ($name !== null) {
            return $this->transformationProcessor->transformByGroup($name, 'lowercase');
        }

        return null;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function copy(CatalogCopyStruct $copyStruct): int
    {
        $fromCatalog = $this->findById($copyStruct->id);

        $id = $this->doInsert([
            StorageSchema::COLUMN_IDENTIFIER => $copyStruct->identifier,
            StorageSchema::COLUMN_CREATOR_ID => $copyStruct->creatorId,
            StorageSchema::COLUMN_CREATED => time(),
            StorageSchema::COLUMN_MODIFIED => time(),
            StorageSchema::COLUMN_QUERY_STRING => $fromCatalog['query_string'] ?? '',
        ]);

        $this->translationGateway->copy($copyStruct->id, $id);

        return $id;
    }
}
