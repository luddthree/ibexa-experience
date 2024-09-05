<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadata;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadataRegistryInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\ProductPriceCreateStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Update\Struct\ProductPriceUpdateStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\GatewayInterface;
use Ibexa\ProductCatalog\Money\DecimalMoneyFactory;

/**
 * @phpstan-import-type Data from \Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\GatewayInterface
 *
 * @phpstan-extends \Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase<Data>
 */
final class DoctrineDatabase extends AbstractDoctrineDatabase implements GatewayInterface
{
    private DecimalMoneyFactory $decimalMoneyFactory;

    /**
     * @var iterable<\Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\Inheritance\PersisterInterface>
     */
    private iterable $inheritancePersisters;

    /**
     * @param iterable<\Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\Inheritance\PersisterInterface> $inheritancePersisters
     */
    public function __construct(
        Connection $connection,
        DoctrineSchemaMetadataRegistryInterface $registry,
        DecimalMoneyFactory $decimalMoneyFactory,
        iterable $inheritancePersisters
    ) {
        parent::__construct($connection, $registry);
        $this->decimalMoneyFactory = $decimalMoneyFactory;
        $this->inheritancePersisters = $inheritancePersisters;
    }

    protected function getTableName(): string
    {
        return StorageSchema::TABLE_NAME;
    }

    protected function getTableAlias(): string
    {
        return 'ipsp';
    }

    protected function buildMetadata(): DoctrineSchemaMetadata
    {
        $columnToTypesMap = [
            StorageSchema::COLUMN_ID => Types::INTEGER,
            StorageSchema::COLUMN_PRODUCT_CODE => Types::STRING,
            StorageSchema::COLUMN_CURRENCY_ID => Types::INTEGER,
            StorageSchema::COLUMN_AMOUNT => Types::DECIMAL,
            StorageSchema::COLUMN_CUSTOM_PRICE => Types::DECIMAL,
            StorageSchema::COLUMN_DISCR => Types::STRING,
            StorageSchema::COLUMN_CUSTOM_PRICE_RULE => Types::DECIMAL,
        ];
        $identifierColumns = [StorageSchema::COLUMN_ID];

        $metadata = new DoctrineSchemaMetadata(
            $this->connection,
            PriceInterface::class,
            $this->getTableName(),
            $columnToTypesMap,
            $identifierColumns,
        );

        foreach ($this->inheritancePersisters as $inheritancePersister) {
            $inheritancePersister->addInheritanceMetadata($metadata);
        }

        return $metadata;
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?array
    {
        $orderBy ??= [StorageSchema::COLUMN_ID => 'DESC'];

        return parent::findOneBy($criteria, $orderBy);
    }

    public function findOneByProductCode(
        string $productCode,
        int $currencyId,
        string $discriminator,
        array $criteria
    ): ?array {
        $criteria = $criteria + [
            StorageSchema::COLUMN_PRODUCT_CODE => $productCode,
            StorageSchema::COLUMN_CURRENCY_ID => $currencyId,
            StorageSchema::COLUMN_DISCR => $discriminator,
        ];

        return $this->findOneBy($criteria);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function insert(ProductPriceCreateStructInterface $createStruct): int
    {
        $formatter = $this->decimalMoneyFactory->getMoneyFormatter();
        $metadata = $this->getMetadata();
        $customPrice = $createStruct->getCustomPrice();
        $customPriceRule = $createStruct->getCustomPriceRule();

        $id = $this->doInsert([
            StorageSchema::COLUMN_PRODUCT_CODE => $createStruct->getProduct()->getCode(),
            StorageSchema::COLUMN_AMOUNT => $formatter->format($createStruct->getMoney()),
            StorageSchema::COLUMN_CURRENCY_ID => $createStruct->getCurrency()->getId(),
            StorageSchema::COLUMN_DISCR => $createStruct::getDiscriminator(),
            StorageSchema::COLUMN_CUSTOM_PRICE => $customPrice === null ? null : $formatter->format($customPrice),
            StorageSchema::COLUMN_CUSTOM_PRICE_RULE => $customPriceRule,
        ]);

        foreach ($this->inheritancePersisters as $inheritancePersister) {
            if ($inheritancePersister->canHandle($createStruct)) {
                $inheritancePersister->handleProductPriceCreate($id, $this->connection, $metadata, $createStruct);
            }
        }

        return $id;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function update(ProductPriceUpdateStructInterface $updateStruct): void
    {
        $data = [];
        if ($updateStruct->getMoney() !== null) {
            $formatter = $this->decimalMoneyFactory->getMoneyFormatter();
            $data[StorageSchema::COLUMN_AMOUNT] = $formatter->format($updateStruct->getMoney());
        }

        $customPriceMoney = $updateStruct->getCustomPriceMoney();
        if ($customPriceMoney !== null) {
            $formatter = $this->decimalMoneyFactory->getMoneyFormatter();
            $customPriceMoney = $formatter->format($customPriceMoney);
        }

        $data[StorageSchema::COLUMN_CUSTOM_PRICE] = $customPriceMoney;
        $data[StorageSchema::COLUMN_CUSTOM_PRICE_RULE] = $updateStruct->getCustomPriceRule();

        $criteria = [
            StorageSchema::COLUMN_ID => $updateStruct->getPrice()->getId(),
        ];

        $this->doUpdate($criteria, $data);
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

    public function updateProductCode(string $newProductCode, string $oldProductCode): void
    {
        $data[StorageSchema::COLUMN_PRODUCT_CODE] = $newProductCode;

        $criteria = [
            StorageSchema::COLUMN_PRODUCT_CODE => $oldProductCode,
        ];

        $this->doUpdate($criteria, $data);
    }
}
