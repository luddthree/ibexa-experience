<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Persistence\Legacy\CustomerGroup;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupUpdateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup\HandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\CustomerGroup;
use Ibexa\Tests\Integration\ProductCatalog\Fixtures\CustomerGroupFixture;
use Ibexa\Tests\Integration\ProductCatalog\IbexaKernelTestCase;

/**
 * @covers \Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup\Handler
 */
final class HandlerTest extends IbexaKernelTestCase
{
    private HandlerInterface $handler;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->handler = self::getServiceByClassName(HandlerInterface::class);
    }

    /**
     * @depends testFind
     */
    public function testCreate(): void
    {
        $createStruct = new CustomerGroupCreateStruct(
            'bar',
            [2 => 'Bar'],
            [2 => 'Lorem Ipsum 2'],
            '42'
        );

        $id = $this->handler->create($createStruct);
        $customerGroup = $this->handler->find($id);

        self::assertInstanceOf(CustomerGroup::class, $customerGroup);
        self::assertSame('bar', $customerGroup->identifier);
        self::assertSame('Bar', $customerGroup->getName(2));
        self::assertSame('Lorem Ipsum 2', $customerGroup->getDescription(2));
        self::assertStringStartsWith('42', $customerGroup->globalPriceRate);
    }

    /**
     * @depends testFind
     */
    public function testUpdate(): void
    {
        $updateStruct = new CustomerGroupUpdateStruct(
            CustomerGroupFixture::FIXTURE_ENTRY_ID,
            'bar',
            [2 => 'Bar'],
            [2 => 'Lorem Ipsum 2'],
            '42',
        );

        $this->handler->update($updateStruct);
        $customerGroup = $this->handler->find(CustomerGroupFixture::FIXTURE_ENTRY_ID);

        self::assertInstanceOf(CustomerGroup::class, $customerGroup);
        self::assertSame('bar', $customerGroup->identifier);
        self::assertSame('Bar', $customerGroup->getName(2));
        self::assertSame('Lorem Ipsum 2', $customerGroup->getDescription(2));
        self::assertStringStartsWith('42', $customerGroup->globalPriceRate);
    }

    /**
     * @depends testFind
     */
    public function testDelete(): void
    {
        $this->handler->find(CustomerGroupFixture::FIXTURE_ENTRY_ID);
        $this->handler->delete(CustomerGroupFixture::FIXTURE_ENTRY_ID);

        $this->expectException(NotFoundException::class);
        $this->handler->find(CustomerGroupFixture::FIXTURE_ENTRY_ID);
    }

    public function testFind(): void
    {
        $customerGroup = $this->handler->find(CustomerGroupFixture::FIXTURE_ENTRY_ID);
        self::assertSame(CustomerGroupFixture::FIXTURE_ENTRY_IDENTIFIER, $customerGroup->identifier);
        self::assertSame('Answer To Life, Universe and Everything', $customerGroup->getName(2));
        self::assertSame('', $customerGroup->getDescription(2));
    }

    public function testThrowsWhenFindingNonExistentEntry(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("Could not find 'Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface' with identifier '-1'");
        $this->handler->find(-1);
    }

    public function testFindAll(): void
    {
        $results = $this->handler->findAll();
        self::assertNotCount(0, $results);
        self::assertContainsOnlyInstancesOf(CustomerGroup::class, $results);
    }
}
