<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductTypeVatCategory;

use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductTypeVatCategory;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductTypeVatCategoryCreateStruct;

final class Handler implements HandlerInterface
{
    private GatewayInterface $gateway;

    private Mapper $mapper;

    public function __construct(GatewayInterface $gateway, Mapper $mapper)
    {
        $this->gateway = $gateway;
        $this->mapper = $mapper;
    }

    public function findByFieldDefinitionId(int $fieldDefinitionId, int $status): array
    {
        return $this->findBy([
            'field_definition_id' => $fieldDefinitionId,
            'status' => $status,
        ]);
    }

    public function deleteByFieldDefinitionId(int $fieldDefinitionId, int $status): void
    {
        $this->gateway->deleteBy([
            'field_definition_id' => $fieldDefinitionId,
            'status' => $status,
        ]);
    }

    public function findAll(?int $limit = null, int $offset = 0): array
    {
        return $this->findBy([], [], $limit, $offset);
    }

    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, int $offset = 0): array
    {
        $rows = $this->gateway->findBy($criteria, $orderBy, $limit, $offset);

        return $this->mapper->createFromRows($rows);
    }

    public function countBy(array $criteria): int
    {
        return $this->gateway->countBy($criteria);
    }

    public function find(int $id): object
    {
        $row = $this->gateway->findById($id);

        if ($row === null) {
            throw new NotFoundException(ProductTypeVatCategory::class, $id);
        }

        return $this->mapper->createFromRow($row);
    }

    public function exists(int $id): bool
    {
        return $this->gateway->findById($id) !== null;
    }

    public function create(ProductTypeVatCategoryCreateStruct $createStruct): int
    {
        return $this->gateway->insert($createStruct);
    }

    public function delete(int $id): void
    {
        $this->gateway->delete($id);
    }

    public function deleteBy(array $criteria): void
    {
        $this->gateway->deleteBy($criteria);
    }
}
