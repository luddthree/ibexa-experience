<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Persistence\Legacy\ProductPrice;

use Ibexa\ProductCatalog\Exception\MissingHandlingServiceException;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Currency\HandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\Mapper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\Mapper
 */
final class MapperTest extends TestCase
{
    private Mapper $mapper;

    protected function setUp(): void
    {
        $currencyHandler = $this->createMock(HandlerInterface::class);
        $this->mapper = new Mapper(
            $currencyHandler,
            [],
        );
    }

    public function testCreateFromRow(): void
    {
        $this->expectException(MissingHandlingServiceException::class);
        $this->expectExceptionMessage(
            'Missing "Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\Inheritance\MapperInterface" '
            . 'handler for "array". Available handlers: "". Ensure that your service handler has '
            . '"ibexa.product_catalog.product_price.inheritance.mapper" tag'
        );

        $row = $this->getSampleRow();
        $this->mapper->createFromRow($row);
    }

    public function testCreateFromRows(): void
    {
        $this->expectException(MissingHandlingServiceException::class);
        $this->expectExceptionMessage(
            'Missing "Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\Inheritance\MapperInterface" '
            . 'handler for "array". Available handlers: "". Ensure that your service handler has '
            . '"ibexa.product_catalog.product_price.inheritance.mapper" tag'
        );

        $row = $this->getSampleRow();
        $this->mapper->createFromRows([$row]);
    }

    /**
     * @phpstan-return array{
     *     id: int,
     *     amount: numeric-string,
     *     custom_price_amount: numeric-string|null,
     *     custom_price_rule: numeric-string|null,
     *     currency_id: int,
     *     product_code: non-empty-string,
     *     discriminator: string,
     * }
     */
    private function getSampleRow(): array
    {
        return [
            'id' => 1,
            'amount' => '4200',
            'custom_price_amount' => null,
            'custom_price_rule' => null,
            'currency_id' => 1,
            'product_code' => 'FOO',
            'discriminator' => '__UNKNOWN__',
        ];
    }
}
