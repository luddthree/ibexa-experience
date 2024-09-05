<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadata;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadataInterface;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadataRegistryInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Core\Persistence\TransformationProcessor;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup\Gateway\Translation\GatewayInterface as TranslationGateway;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup\Gateway\Translation\Schema as TranslationSchema;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup\GatewayInterface;

/**
 * @extends \Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase<array{
 *     id: int,
 *     identifier: string,
 *     global_price_rate: numeric-string,
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
        return 'icg';
    }

    protected function buildMetadata(): DoctrineSchemaMetadataInterface
    {
        $columnToTypesMap = [
            StorageSchema::COLUMN_ID => Types::INTEGER,
            StorageSchema::COLUMN_IDENTIFIER => Types::STRING,
            StorageSchema::COLUMN_GLOBAL_PRICE_RATE => Types::DECIMAL,
        ];
        $identifierColumns = [StorageSchema::COLUMN_ID];

        $translationMetadata = $this->translationGateway->getMetadata();

        $metadata = new DoctrineSchemaMetadata(
            $this->connection,
            CustomerGroupInterface::class,
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
    public function insert(CustomerGroupCreateStruct $createStruct): int
    {
        $id = $this->doInsert([
            StorageSchema::COLUMN_IDENTIFIER => $createStruct->getIdentifier(),
            StorageSchema::COLUMN_GLOBAL_PRICE_RATE => $createStruct->getGlobalPriceRate(),
        ]);

        foreach ($createStruct->getNames() as $languageId => $name) {
            $this->translationGateway->insert(
                $id,
                $languageId,
                [
                    TranslationSchema::COLUMN_NAME => $name,
                    TranslationSchema::COLUMN_NAME_NORMALIZED => $this->getNormalizedName($name),
                    TranslationSchema::COLUMN_DESCRIPTION => $createStruct->getDescription($languageId),
                ]
            );
        }

        return $id;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function update(CustomerGroupUpdateStruct $updateStruct): void
    {
        $data = [
            StorageSchema::COLUMN_IDENTIFIER => $updateStruct->getIdentifier(),
            StorageSchema::COLUMN_GLOBAL_PRICE_RATE => $updateStruct->getGlobalPriceRate(),
        ];

        $data = array_filter($data, static function ($value): bool {
            return !empty($value) || $value === '0';
        });

        $criteria = [
            StorageSchema::COLUMN_ID => $updateStruct->getId(),
        ];

        if (!empty($data)) {
            $this->doUpdate($criteria, $data);
        }

        foreach ($updateStruct->getNames() as $languageId => $name) {
            $this->translationGateway->save(
                $updateStruct->getId(),
                $languageId,
                [
                    TranslationSchema::COLUMN_NAME => $name,
                    TranslationSchema::COLUMN_NAME_NORMALIZED => $this->getNormalizedName($name),
                    TranslationSchema::COLUMN_DESCRIPTION => $updateStruct->getDescription($languageId),
                ]
            );
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
}
