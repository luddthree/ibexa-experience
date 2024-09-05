<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Persistence\Legacy\ProductPrice\CustomerGroup;

use Doctrine\DBAL\Connection;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadata;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadataInterface;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\CustomerGroupPriceCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\ProductPriceCreateStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\CustomerGroup\Persister;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\CustomerGroup\Persister
 */
final class PersisterTest extends TestCase
{
    private Persister $persister;

    protected function setUp(): void
    {
        $this->persister = new Persister();
    }

    public function testLoadMetadata(): void
    {
        $metadata = $this->createMock(DoctrineSchemaMetadataInterface::class);

        $metadata->expects(self::once())
            ->method('addSubclass')
            ->with(
                self::identicalTo('customer_group'),
                self::isInstanceOf(DoctrineSchemaMetadata::class),
            );

        $this->persister->addInheritanceMetadata($metadata);
    }

    public function testCanHandle(): void
    {
        $struct = $this->createMock(ProductPriceCreateStructInterface::class);
        self::assertFalse($this->persister->canHandle($struct));

        $struct = new CustomerGroupPriceCreateStruct(
            $this->createMock(CustomerGroupInterface::class),
            $this->createMock(ProductInterface::class),
            $this->createMock(CurrencyInterface::class),
            new Money('4200', new Currency('FOO')),
            null,
            null
        );
        self::assertTrue($this->persister->canHandle($struct));
    }

    public function testHandling(): void
    {
        $struct = new CustomerGroupPriceCreateStruct(
            $this->createMock(CustomerGroupInterface::class),
            $this->createMock(ProductInterface::class),
            $this->createMock(CurrencyInterface::class),
            new Money('4200', new Currency('FOO')),
            null,
            null
        );

        $connection = $this->createMock(Connection::class);

        $subclassMetadata = $this->createMock(DoctrineSchemaMetadataInterface::class);

        $metadata = $this->createMock(DoctrineSchemaMetadataInterface::class);
        $metadata->expects(self::once())
            ->method('getSubclassByDiscriminator')
            ->with(self::identicalTo('customer_group'))
            ->willReturn($subclassMetadata);

        $subclassMetadata->expects(self::once())
            ->method('getIdentifierColumn')
            ->willReturn('__FOO_COLUMN__');

        $expectedData = [
            '__FOO_COLUMN__' => 1,
            'customer_group_id' => 0,
        ];
        $expectedTypes = [
            '__FOO_COLUMN__' => 0,
        ];
        $connection->expects(self::once())
            ->method('insert')
            ->with(
                self::identicalTo(''),
                self::identicalTo($expectedData),
                self::identicalTo($expectedTypes),
            );

        $this->persister->handleProductPriceCreate(1, $connection, $metadata, $struct);
    }
}
