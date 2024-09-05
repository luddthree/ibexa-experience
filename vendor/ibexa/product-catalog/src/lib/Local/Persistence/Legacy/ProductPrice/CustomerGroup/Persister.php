<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\CustomerGroup;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadata;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadataInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\CustomerGroupPriceCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\ProductPriceCreateStructInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\Inheritance\PersisterInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductPrice\CustomerGroupPrice;

final class Persister implements PersisterInterface
{
    private const TABLE_NAME = 'ibexa_product_specification_price_customer_group';

    public function canHandle(ProductPriceCreateStructInterface $struct): bool
    {
        return $struct instanceof CustomerGroupPriceCreateStruct;
    }

    public function handleProductPriceCreate(
        int $id,
        Connection $connection,
        DoctrineSchemaMetadataInterface $metadata,
        ProductPriceCreateStructInterface $struct
    ): void {
        assert($struct instanceof CustomerGroupPriceCreateStruct);

        $discriminator = CustomerGroupPriceCreateStruct::getDiscriminator();
        $subclassMetadata = $metadata->getSubclassByDiscriminator($discriminator);
        $identifierColumnName = $subclassMetadata->getIdentifierColumn();
        $data = [
            $identifierColumnName => $id,
            'customer_group_id' => $struct->getCustomerGroup()->getId(),
        ];
        $types = [
            $identifierColumnName => $subclassMetadata->getBindingTypeForColumn('customer_group_id'),
        ];

        $connection->insert(
            $subclassMetadata->getTableName(),
            $data,
            $types,
        );
    }

    public function addInheritanceMetadata(DoctrineSchemaMetadataInterface $metadata): void
    {
        $identifierColumns = [
            'id',
        ];
        $columnToTypesMap = [
            'customer_group_id' => Types::INTEGER,
        ];

        $metadata->addSubclass(CustomerGroupPriceCreateStruct::getDiscriminator(), new DoctrineSchemaMetadata(
            $metadata->getConnection(),
            CustomerGroupPrice::class,
            self::TABLE_NAME,
            $columnToTypesMap,
            $identifierColumns,
        ));
    }
}
