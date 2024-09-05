<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Currency;

use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\ProductCatalog\Local\Persistence\Values\Currency;
use Ibexa\ProductCatalog\Local\Persistence\Values\CurrencyCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\CurrencyUpdateStruct;

final class Handler implements HandlerInterface
{
    private GatewayInterface $gateway;

    private Mapper $mapper;

    public function __construct(GatewayInterface $gateway, Mapper $mapper)
    {
        $this->gateway = $gateway;
        $this->mapper = $mapper;
    }

    public function findAll(?int $limit = null, int $offset = 0): array
    {
        $results = $this->gateway->findAll($limit, $offset);

        return $this->mapper->createFromRows($results);
    }

    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, int $offset = 0): array
    {
        $results = $this->gateway->findBy($criteria, $orderBy, $limit, $offset);

        return $this->mapper->createFromRows($results);
    }

    public function find(int $id): object
    {
        $row = $this->gateway->findById($id);

        if ($row === null) {
            throw new NotFoundException(Currency::class, $id);
        }

        return $this->mapper->createFromRow($row);
    }

    public function exists(int $id): bool
    {
        return $this->gateway->findById($id) !== null;
    }

    public function countAll(): int
    {
        return $this->gateway->countAll();
    }

    public function countBy(array $criteria): int
    {
        return $this->gateway->countBy($criteria);
    }

    public function create(CurrencyCreateStruct $struct): Currency
    {
        $row = $this->gateway->create($struct);

        return $this->mapper->createFromRow($row);
    }

    public function delete(int $id): void
    {
        $this->gateway->delete($id);
    }

    public function update(CurrencyUpdateStruct $struct): Currency
    {
        $row = $this->gateway->update($struct);

        return $this->mapper->createFromRow($row);
    }
}
