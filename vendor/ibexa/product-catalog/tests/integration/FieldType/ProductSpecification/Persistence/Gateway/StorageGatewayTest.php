<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\FieldType\ProductSpecification\Persistence\Gateway;

use Ibexa\ProductCatalog\FieldType\ProductSpecification\Persistence\Gateway\StorageGateway;
use Ibexa\Tests\Integration\ProductCatalog\IbexaKernelTestCase;

/**
 * @covers \Ibexa\ProductCatalog\FieldType\ProductSpecification\Persistence\Gateway\StorageGateway
 */
final class StorageGatewayTest extends IbexaKernelTestCase
{
    private const ADMIN_FIELD_ID = 28;
    private const ADMIN_PREVIOUS_VERSION = 3;
    private const ADMIN_CONTENT_ID = 14;

    private StorageGateway $storageGateway;

    private int $productId;

    protected function setUp(): void
    {
        self::bootKernel();
        $connection = self::getDoctrineConnection();
        $connection->insert('ibexa_product', [
            'code' => 'foo',
        ]);
        $data = $connection->executeQuery('SELECT id FROM ibexa_product WHERE code = :code', [
            'code' => 'foo',
        ])->fetchAssociative();

        if ($data === false) {
            self::fail('Missing Product data in "ibexa_product" table.');
        }

        $this->productId = (int)$data['id'];
        $connection->insert('ibexa_product_specification', [
            'product_id' => $this->productId,
            'code' => 'foo',
            'content_id' => self::ADMIN_CONTENT_ID,
            'version_no' => self::ADMIN_PREVIOUS_VERSION,
            'field_id' => self::ADMIN_FIELD_ID,
        ]);

        $this->storageGateway = self::getServiceByClassName(StorageGateway::class);
    }

    /**
     * @depends testExists
     */
    public function testUpdate(): void
    {
        self::assertTrue($this->storageGateway->exists(self::ADMIN_CONTENT_ID, self::ADMIN_PREVIOUS_VERSION));
        $productSpecification = $this->storageGateway->findByContentIdAndVersionNo(
            self::ADMIN_CONTENT_ID,
            self::ADMIN_PREVIOUS_VERSION,
        );
        self::assertNotNull($productSpecification);
        self::assertSame('foo', $productSpecification['code']);

        $this->storageGateway->update(
            self::ADMIN_CONTENT_ID,
            self::ADMIN_PREVIOUS_VERSION,
            self::ADMIN_FIELD_ID,
            'bar',
        );

        $productSpecification = $this->storageGateway->findByContentIdAndVersionNo(
            self::ADMIN_CONTENT_ID,
            self::ADMIN_PREVIOUS_VERSION,
        );
        self::assertNotNull($productSpecification);
        self::assertSame('bar', $productSpecification['code']);
    }

    /**
     * @depends testExists
     */
    public function testDelete(): void
    {
        self::assertTrue($this->storageGateway->exists(self::ADMIN_CONTENT_ID, self::ADMIN_PREVIOUS_VERSION));
        $this->storageGateway->delete(
            self::ADMIN_CONTENT_ID,
            self::ADMIN_PREVIOUS_VERSION,
            self::ADMIN_FIELD_ID,
        );
        self::assertFalse($this->storageGateway->exists(self::ADMIN_CONTENT_ID, self::ADMIN_PREVIOUS_VERSION));
    }

    /**
     * @depends testExists
     */
    public function testInsert(): void
    {
        self::assertFalse($this->storageGateway->exists(self::ADMIN_CONTENT_ID, 4));
        $this->storageGateway->insert(
            $this->productId,
            self::ADMIN_CONTENT_ID,
            4,
            self::ADMIN_FIELD_ID,
            'foo',
        );
        self::assertTrue($this->storageGateway->exists(self::ADMIN_CONTENT_ID, 4));
    }

    public function testFindByContentIdAndVersionNo(): void
    {
        self::assertNotNull($this->storageGateway->findByContentIdAndVersionNo(self::ADMIN_CONTENT_ID, self::ADMIN_PREVIOUS_VERSION));
        self::assertNull($this->storageGateway->findByContentIdAndVersionNo(-1, -1));
    }

    public function testExists(): void
    {
        self::assertTrue($this->storageGateway->exists(self::ADMIN_CONTENT_ID, self::ADMIN_PREVIOUS_VERSION));
        self::assertFalse($this->storageGateway->exists(-1, -1));
    }
}
