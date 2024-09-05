<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice;

use Ibexa\ProductCatalog\Exception\MissingHandlingServiceException;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Currency\HandlerInterface as CurrencyHandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\Inheritance\MapperInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\AbstractProductPrice;

final class Mapper
{
    private CurrencyHandlerInterface $currencyHandler;

    /** @var iterable<\Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\Inheritance\MapperInterface> */
    private iterable $mappers;

    /**
     * @param iterable<\Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\Inheritance\MapperInterface> $mappers
     */
    public function __construct(
        CurrencyHandlerInterface $currencyHandler,
        iterable $mappers
    ) {
        $this->currencyHandler = $currencyHandler;
        $this->mappers = $mappers;
    }

    /**
     * @phpstan-param array<array{
     *   id: int,
     *   amount: numeric-string,
     *   custom_price_amount: numeric-string|null,
     *   custom_price_rule: numeric-string|null,
     *   currency_id: int,
     *   product_code: non-empty-string,
     *   discriminator: string,
     *   customer_group_id?: int,
     * }> $rows
     *
     * @return \Ibexa\ProductCatalog\Local\Persistence\Values\AbstractProductPrice[]
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function createFromRows(array $rows): array
    {
        $prices = [];
        foreach ($rows as $row) {
            $prices[] = $this->createFromRow($row);
        }

        return $prices;
    }

    /**
     * @phpstan-param array{
     *   id: int,
     *   amount: numeric-string,
     *   custom_price_amount: numeric-string|null,
     *   custom_price_rule: numeric-string|null,
     *   currency_id: int,
     *   product_code: non-empty-string,
     *   discriminator: string,
     *   customer_group_id?: int,
     * } $row
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \LogicException
     */
    public function createFromRow(array $row): AbstractProductPrice
    {
        $discriminator = $row['discriminator'];

        foreach ($this->mappers as $mapper) {
            if ($mapper->canHandleResultSet($discriminator)) {
                $currency = $this->currencyHandler->find($row['currency_id']);

                return $mapper->handleResultSet($discriminator, $row, $currency);
            }
        }

        throw new MissingHandlingServiceException(
            $this->mappers,
            $row,
            MapperInterface::class,
            'ibexa.product_catalog.product_price.inheritance.mapper',
        );
    }
}
