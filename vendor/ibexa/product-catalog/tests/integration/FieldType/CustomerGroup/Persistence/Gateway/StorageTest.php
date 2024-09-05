<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\FieldType\CustomerGroup\Persistence\Gateway;

use Ibexa\Contracts\Core\Persistence\Content\ContentInfo;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;
use Ibexa\ProductCatalog\FieldType\CustomerGroup\Persistence\Gateway\Storage;
use Ibexa\Tests\Integration\ProductCatalog\Fixtures\ContentCustomerGroupFixture;
use Ibexa\Tests\Integration\ProductCatalog\Fixtures\CustomerGroupFixture;
use Ibexa\Tests\Integration\ProductCatalog\IbexaKernelTestCase;

/**
 * @covers \Ibexa\ProductCatalog\FieldType\CustomerGroup\Persistence\Gateway\Storage
 */
final class StorageTest extends IbexaKernelTestCase
{
    private Storage $storage;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->storage = self::getServiceByClassName(Storage::class);
    }

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function testDeleteFieldData(): void
    {
        $versionId = ContentCustomerGroupFixture::FIXTURE_FIELD_ID;
        $versionNo = ContentCustomerGroupFixture::FIXTURE_VERSION_NO;

        $this->assertCustomerGroupContentLinkExists($versionId, $versionNo);

        $versionInfo = new VersionInfo([
            'versionNo' => $versionNo,
        ]);
        $this->storage->deleteFieldData($versionInfo, [$versionId], []);

        $this->assertCustomerGroupContentLinkNotExists($versionId, $versionNo);
    }

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function testGetFieldData(): void
    {
        $versionInfo = new VersionInfo();
        $field = $this->buildField(
            ContentCustomerGroupFixture::FIXTURE_FIELD_ID,
            ContentCustomerGroupFixture::FIXTURE_VERSION_NO,
        );
        $this->storage->getFieldData($versionInfo, $field, []);

        self::assertSame([
            'customer_group_id' => 42,
        ], $field->value->externalData);
    }

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function testStoreFieldRemovesConnectionIfFieldDataIsEmpty(): void
    {
        $fieldId = ContentCustomerGroupFixture::FIXTURE_FIELD_ID;
        $versionNo = ContentCustomerGroupFixture::FIXTURE_VERSION_NO;

        $this->assertCustomerGroupContentLinkExists($fieldId, $versionNo);

        $versionInfo = new VersionInfo();
        $versionInfo->contentInfo = new ContentInfo();
        $versionInfo->contentInfo->id = 1;

        $field = $this->buildField($fieldId, $versionNo);

        $this->storage->storeFieldData($versionInfo, $field, []);

        $this->assertCustomerGroupContentLinkNotExists($fieldId, $versionNo);
    }

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function testStoreFieldCreatesConnection(): void
    {
        $versionNo = 3;
        $fieldId = ContentCustomerGroupFixture::FIXTURE_FIELD_ID;

        $this->assertCustomerGroupContentLinkNotExists($fieldId, $versionNo);

        $versionInfo = new VersionInfo();
        $versionInfo->contentInfo = new ContentInfo();
        $versionInfo->contentInfo->id = 1;

        $field = $this->buildField($fieldId, $versionNo, [
            'customer_group_id' => CustomerGroupFixture::FIXTURE_ENTRY_ID,
        ]);

        $this->storage->storeFieldData($versionInfo, $field, []);

        $this->assertCustomerGroupContentLinkExists($fieldId, $versionNo);
    }

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function testStoreFieldUpdatesConnection(): void
    {
        $fieldId = ContentCustomerGroupFixture::FIXTURE_FIELD_ID;
        $versionNo = ContentCustomerGroupFixture::FIXTURE_VERSION_NO;

        $this->assertCustomerGroupContentLinkExists($fieldId, $versionNo);
        $this->assertAssignedCustomerGroupIsSame(
            $fieldId,
            $versionNo,
            ContentCustomerGroupFixture::FIXTURE_CUSTOMER_GROUP_ID
        );

        $versionInfo = new VersionInfo();
        $versionInfo->contentInfo = new ContentInfo();
        $versionInfo->contentInfo->id = 1;

        $differentCustomerGroupId = CustomerGroupFixture::FIXTURE_SECOND_ENTRY_ID;
        $field = $this->buildField($fieldId, $versionNo, [
            'customer_group_id' => $differentCustomerGroupId,
        ]);

        $this->storage->storeFieldData($versionInfo, $field, []);

        $this->assertCustomerGroupContentLinkExists($fieldId, $versionNo);
        $this->assertAssignedCustomerGroupIsSame(
            $fieldId,
            $versionNo,
            $differentCustomerGroupId
        );
    }

    /**
     * @param array<mixed>|null $externalData
     */
    private function buildField(int $versionId, int $versionNo, ?array $externalData = null): Field
    {
        $field = new Field([
            'id' => $versionId,
            'versionNo' => $versionNo,
        ]);
        $field->value = new FieldValue();

        if ($externalData !== null) {
            $field->value->externalData = $externalData;
        }

        return $field;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    private function assertCustomerGroupContentLinkExists(int $versionId, int $versionNo): void
    {
        $field = $this->queryFieldData($versionId, $versionNo);
        self::assertNotNull($field->value->externalData);
    }

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    private function assertCustomerGroupContentLinkNotExists(int $versionId, int $versionNo): void
    {
        $field = $this->queryFieldData($versionId, $versionNo);
        self::assertNull($field->value->externalData);
    }

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    private function assertAssignedCustomerGroupIsSame(
        int $versionId,
        int $versionNo,
        int $expectedCustomerGroupId
    ): void {
        $field = $this->queryFieldData($versionId, $versionNo);
        self::assertEquals($expectedCustomerGroupId, $field->value->externalData['customer_group_id']);
    }

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    private function queryFieldData(int $versionId, int $versionNo): Field
    {
        $versionInfo = new VersionInfo();
        $field = $this->buildField($versionId, $versionNo);
        $this->storage->getFieldData($versionInfo, $field, []);

        return $field;
    }
}
