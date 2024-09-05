<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductTypeSettings;

use Ibexa\Contracts\Core\Persistence\Content\Type;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductTypeSettings\Gateway\StorageSchema;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductTypeSetting;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductTypeSettingCreateStruct;

/**
 * @internal
 */
final class Handler implements HandlerInterface
{
    private GatewayInterface $gateway;

    private Mapper $mapper;

    public function __construct(GatewayInterface $gateway, Mapper $mapper)
    {
        $this->gateway = $gateway;
        $this->mapper = $mapper;
    }

    public function create(ProductTypeSettingCreateStruct $createStruct): int
    {
        return $this->gateway->insert($createStruct);
    }

    public function publish(int $fieldDefinitionId): void
    {
        $this->deleteByFieldDefinitionId($fieldDefinitionId, Type::STATUS_DEFINED);

        $this->gateway->update(
            [
                StorageSchema::COLUMN_STATUS => Type::STATUS_DEFINED,
            ],
            $fieldDefinitionId,
            Type::STATUS_DRAFT
        );
    }

    public function findByFieldDefinitionId(int $fieldDefinitionId, int $status): ProductTypeSetting
    {
        $row = $this->gateway->findOneBy(
            [
                StorageSchema::COLUMN_FIELD_DEFINITION_ID => $fieldDefinitionId,
                StorageSchema::COLUMN_STATUS => $status,
            ]
        );

        if ($row === null) {
            throw new NotFoundException(
                ProductTypeSetting::class,
                [
                    StorageSchema::COLUMN_FIELD_DEFINITION_ID => $fieldDefinitionId,
                    StorageSchema::COLUMN_STATUS => $status,
                ]
            );
        }

        return $this->mapper->createFromRow($row);
    }

    public function deleteByFieldDefinitionId(int $fieldDefinitionId, int $status): void
    {
        $this->gateway->deleteBy(
            [
                StorageSchema::COLUMN_FIELD_DEFINITION_ID => $fieldDefinitionId,
                StorageSchema::COLUMN_STATUS => $status,
            ]
        );
    }

    public function findBy(
        array $criteria,
        ?array $orderBy = null,
        ?int $limit = null,
        int $offset = 0
    ): array {
        return $this->mapper->createFromRows(
            $this->gateway->findBy($criteria, $orderBy, $limit, $offset)
        );
    }

    public function countBy(array $criteria): int
    {
        return $this->gateway->countBy($criteria);
    }

    public function exists(int $id): bool
    {
        return $this->gateway->findById($id) !== null;
    }

    public function findAll(?int $limit = null, int $offset = 0): array
    {
        return $this->mapper->createFromRows(
            $this->gateway->findAll($limit, $offset)
        );
    }

    public function find(int $id): ProductTypeSetting
    {
        $row = $this->gateway->findById($id);

        if ($row === null) {
            throw new NotFoundException(ProductTypeSetting::class, $id);
        }

        return $this->mapper->createFromRow($row);
    }
}
