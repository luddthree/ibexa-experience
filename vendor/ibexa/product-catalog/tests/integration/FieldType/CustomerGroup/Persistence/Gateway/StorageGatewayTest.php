<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\FieldType\CustomerGroup\Persistence\Gateway;

use Ibexa\ProductCatalog\FieldType\CustomerGroup\Persistence\Gateway\StorageGateway;
use Ibexa\Tests\Integration\ProductCatalog\Fixtures\ContentCustomerGroupFixture;
use Ibexa\Tests\Integration\ProductCatalog\IbexaKernelTestCase;

/**
 * @covers \Ibexa\ProductCatalog\FieldType\CustomerGroup\Persistence\Gateway\StorageGateway
 */
final class StorageGatewayTest extends IbexaKernelTestCase
{
    private StorageGateway $storageGateway;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->storageGateway = self::getServiceByClassName(StorageGateway::class);
    }

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function testExists(): void
    {
        self::assertTrue($this->storageGateway->exists(
            ContentCustomerGroupFixture::FIXTURE_FIELD_ID,
            ContentCustomerGroupFixture::FIXTURE_VERSION_NO
        ));
        self::assertFalse($this->storageGateway->exists(
            ContentCustomerGroupFixture::FIXTURE_FIELD_ID,
            -1
        ));
        self::assertFalse($this->storageGateway->exists(
            -1,
            ContentCustomerGroupFixture::FIXTURE_VERSION_NO
        ));
        self::assertFalse($this->storageGateway->exists(
            -1,
            -1
        ));
    }

    /**
     * @depends testExists
     *
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function testDelete(): void
    {
        $entry = $this->storageGateway->findByFieldIdAndVersionNo(
            ContentCustomerGroupFixture::FIXTURE_FIELD_ID,
            ContentCustomerGroupFixture::FIXTURE_VERSION_NO
        );
        self::assertNotNull($entry);

        $this->storageGateway->delete(
            ContentCustomerGroupFixture::FIXTURE_FIELD_ID,
            ContentCustomerGroupFixture::FIXTURE_VERSION_NO
        );

        $entry = $this->storageGateway->findByFieldIdAndVersionNo(
            ContentCustomerGroupFixture::FIXTURE_FIELD_ID,
            ContentCustomerGroupFixture::FIXTURE_VERSION_NO
        );
        self::assertNull($entry);
    }

    /**
     * @depends testFindByContentIdAndVersionNo
     *
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function testDeletingNonExistentEntry(): void
    {
        $entry = $this->storageGateway->findByFieldIdAndVersionNo(-1, -1);
        self::assertNull($entry);

        $this->storageGateway->delete(-1, -1);
    }

    /**
     * @depends testExists
     *
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function testInsert(): void
    {
        self::assertFalse($this->storageGateway->exists(
            ContentCustomerGroupFixture::FIXTURE_FIELD_ID,
            3
        ));

        $this->storageGateway->insert(
            ContentCustomerGroupFixture::FIXTURE_FIELD_ID,
            3,
            ContentCustomerGroupFixture::FIXTURE_CONTENT_ID,
            ContentCustomerGroupFixture::FIXTURE_CUSTOMER_GROUP_ID
        );
        self::assertTrue($this->storageGateway->exists(
            ContentCustomerGroupFixture::FIXTURE_FIELD_ID,
            3
        ));
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function testFindByContentIdAndVersionNo(): void
    {
        $entry = $this->storageGateway->findByFieldIdAndVersionNo(
            ContentCustomerGroupFixture::FIXTURE_FIELD_ID,
            ContentCustomerGroupFixture::FIXTURE_VERSION_NO
        );

        self::assertSame([
            'customer_group_id' => ContentCustomerGroupFixture::FIXTURE_CUSTOMER_GROUP_ID,
        ], $entry);
    }

    /**
     * @depends testFindByContentIdAndVersionNo
     *
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function testUpdate(): void
    {
        $id = 3;
        $entry = $this->storageGateway->findByFieldIdAndVersionNo(
            ContentCustomerGroupFixture::FIXTURE_FIELD_ID,
            ContentCustomerGroupFixture::FIXTURE_VERSION_NO
        );
        self::assertNotNull($entry);
        self::assertNotSame($id, $entry['customer_group_id']);

        $this->storageGateway->update(
            ContentCustomerGroupFixture::FIXTURE_FIELD_ID,
            ContentCustomerGroupFixture::FIXTURE_VERSION_NO,
            $id
        );

        $entry = $this->storageGateway->findByFieldIdAndVersionNo(
            ContentCustomerGroupFixture::FIXTURE_FIELD_ID,
            ContentCustomerGroupFixture::FIXTURE_VERSION_NO
        );
        self::assertNotNull($entry);
        self::assertSame($id, $entry['customer_group_id']);
    }

    /**
     * @depends testFindByContentIdAndVersionNo
     *
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function testUpdatingNonExistentEntry(): void
    {
        $entry = $this->storageGateway->findByFieldIdAndVersionNo(-1, -1);
        self::assertNull($entry);

        $this->storageGateway->update(-1, -1, ContentCustomerGroupFixture::FIXTURE_CUSTOMER_GROUP_ID);
    }
}
