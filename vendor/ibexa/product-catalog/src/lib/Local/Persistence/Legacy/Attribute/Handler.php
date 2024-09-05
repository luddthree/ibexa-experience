<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Attribute;

use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinition\HandlerInterface as AttributeDefinitionHandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\Attribute;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeUpdateStruct;

final class Handler implements HandlerInterface
{
    private GatewayInterface $gateway;

    private Mapper $mapper;

    private AttributeDefinitionHandlerInterface $attributeDefinitionHandler;

    public function __construct(
        GatewayInterface $gateway,
        Mapper $mapper,
        AttributeDefinitionHandlerInterface $attributeDefinitionHandler
    ) {
        $this->gateway = $gateway;
        $this->mapper = $mapper;
        $this->attributeDefinitionHandler = $attributeDefinitionHandler;
    }

    public function findByProduct(int $productSpecificationId): array
    {
        $rows = $this->gateway->findByProduct($productSpecificationId);

        return $this->mapper->extractFromRows($rows);
    }

    public function create(AttributeCreateStruct $createStruct): int
    {
        $attributeDefinitionId = $createStruct->getAttributeDefinitionId();
        if (is_int($attributeDefinitionId)) {
            $spiAttributeDefinition = $this->attributeDefinitionHandler->load($attributeDefinitionId);
        } else {
            $spiAttributeDefinition = $this->attributeDefinitionHandler->loadByIdentifier($attributeDefinitionId);
        }

        return $this->gateway->create($createStruct, $spiAttributeDefinition);
    }

    public function update(AttributeUpdateStruct $updateStruct): void
    {
        $this->gateway->update($updateStruct);
    }

    public function findAll(?int $limit = null, int $offset = 0): array
    {
        $rows = $this->gateway->findAll($limit, $offset);

        return $this->mapper->extractFromRows($rows);
    }

    /**
     * @param array<string, scalar|array<scalar>|\Doctrine\Common\Collections\Expr\Expression> $criteria
     */
    public function findBy(
        array $criteria,
        ?array $orderBy = null,
        ?int $limit = null,
        int $offset = 0
    ): array {
        $rows = $this->gateway->findBy($criteria, $orderBy, $limit, $offset);

        return $this->mapper->extractFromRows($rows);
    }

    /**
     * @param array<string, scalar|array<scalar>|\Doctrine\Common\Collections\Expr\Expression> $criteria
     */
    public function countBy(array $criteria): int
    {
        return $this->gateway->countBy($criteria);
    }

    public function find(int $id): Attribute
    {
        $row = $this->gateway->findById($id);

        if ($row === null) {
            throw new NotFoundException(AttributeInterface::class, $id);
        }

        return $this->mapper->extractFromRow($row);
    }

    public function exists(int $id): bool
    {
        return $this->gateway->findById($id) !== null;
    }

    public function getProductCount(int $id): int
    {
        return $this->gateway->countProductsContainingAttribute($id);
    }

    public function getProductCountByAttributeGroup(int $id): int
    {
        return $this->gateway->countProductsContainingAttributeGroup($id);
    }

    public function deleteByAttributeDefinition(int $attributeDefinitionId): void
    {
        $this->gateway->deleteByAttributeDefinition($attributeDefinitionId);
    }
}
