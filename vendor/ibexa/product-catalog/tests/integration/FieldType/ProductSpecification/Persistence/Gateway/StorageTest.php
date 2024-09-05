<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\FieldType\ProductSpecification\Persistence\Gateway;

use Doctrine\Common\Collections\Expr\Comparison;
use Ibexa\Contracts\Core\Persistence\Content\ContentInfo;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Persistence\Gateway\Storage;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Persistence\Gateway\StorageGateway;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Attribute\GatewayInterface;
use Ibexa\Tests\Integration\ProductCatalog\IbexaKernelTestCase;
use Webmozart\Assert\Assert;

/**
 * @covers \Ibexa\ProductCatalog\FieldType\ProductSpecification\Persistence\Gateway\Storage
 */
final class StorageTest extends IbexaKernelTestCase
{
    private Storage $storage;

    private GatewayInterface $attributeGateway;

    private StorageGateway $productGateway;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->storage = self::getServiceByClassName(Storage::class);
        $this->attributeGateway = self::getServiceByClassName(GatewayInterface::class);
        $this->productGateway = self::getServiceByClassName(StorageGateway::class);
    }

    public function testGetFieldData(): void
    {
        $result = $this->productGateway->findByCode('0001');
        self::assertNotNull($result);

        $contentId = $result['content_id'];
        $versionNo = $result['version_no'];
        $fieldId = $result['field_id'];
        $contentInfo = new ContentInfo([
            'id' => $contentId,
        ]);
        $versionInfo = new VersionInfo([
            'versionNo' => $versionNo,
            'contentInfo' => $contentInfo,
        ]);
        $field = $this->buildField(
            $fieldId,
            $versionNo,
        );
        $this->storage->getFieldData($versionInfo, $field, []);

        self::assertSame([
            'id' => 1,
            'product_id' => 1,
            'code' => '0001',
            'content_id' => $contentId,
            'version_no' => $versionNo,
            'field_id' => $fieldId,
            'base_product_id' => null,
            'attributes' => [
                1 => 42,
            ],
        ], $field->value->externalData);

        $attributes = $this->getAttributeDataForProductCode('0001');

        self::assertCount(1, $attributes);

        [$attribute] = $attributes;
        self::assertSame(1, $attribute['product_specification_id']);
        self::assertSame(1, $attribute['attribute_definition_id']);
        self::assertSame('integer', $attribute['discriminator']);
        self::assertSame(42, $attribute['integer_value']);
        self::assertSame(1, $attribute['integer_id']);
    }

    public function testDeleteFieldData(): void
    {
        $result = $this->productGateway->findByCode('0001');
        self::assertNotNull($result);

        $contentId = $result['content_id'];
        $versionNo = $result['version_no'];
        $fieldId = $result['field_id'];
        $versionInfo = new VersionInfo([
            'versionNo' => 1,
            'contentInfo' => new ContentInfo([
                'id' => $contentId,
            ]),
        ]);
        $field = $this->buildField(
            $fieldId,
            $versionNo,
        );
        $this->storage->getFieldData($versionInfo, $field, []);

        $versionInfo = new VersionInfo([
            'versionNo' => $versionNo,
        ]);
        $this->storage->deleteFieldData($versionInfo, [$fieldId], []);

        $result = $this->productGateway->findByCode('0001');
        self::assertNull($result);
    }

    public function testStoreFieldRemovesConnectionIfFieldDataIsEmpty(): void
    {
        $result = $this->productGateway->findByCode('0001');
        self::assertNotNull($result);

        $versionNo = $result['version_no'];
        $fieldId = $result['field_id'];

        $versionInfo = new VersionInfo([
            'versionNo' => $versionNo,
            'contentInfo' => new ContentInfo([
                'id' => $result['content_id'],
            ]),
        ]);

        $field = $this->buildField($fieldId, $versionNo);

        $this->storage->storeFieldData($versionInfo, $field, []);

        $result = $this->productGateway->findByCode('0001');
        self::assertNull($result);
    }

    public function testStoreFieldUpdatesConnection(): void
    {
        $result = $this->productGateway->findByCode('0001');
        self::assertNotNull($result);

        $versionNo = $result['version_no'];
        $fieldId = $result['field_id'];

        $versionInfo = new VersionInfo([
            'contentInfo' => new ContentInfo([
                'id' => $result['content_id'],
            ]),
            'versionNo' => $versionNo,
        ]);

        $field = $this->buildField($fieldId, $versionNo, [
            'code' => '0005',
        ]);

        $this->storage->storeFieldData($versionInfo, $field, []);

        $result = $this->productGateway->findByCode('0001');
        self::assertNull($result);

        $result = $this->productGateway->findByCode('0005');
        self::assertNotNull($result);
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
     * @return array<array{
     *     id: int,
     *     product_specification_id: int,
     *     attribute_definition_id: int,
     *     discriminator: string,
     *     checkbox_id: int|null,
     *     checkbox_value: bool|null,
     *     float_id: int|null,
     *     float_value: float|null,
     *     integer_id: int|null,
     *     integer_value: int|null,
     *     selection_id: int|null,
     *     selection_value: string|null,
     *     color_id: int|null,
     *     color_value: string|null,
     * }>
     */
    private function getAttributeDataForProductCode(string $code): array
    {
        $result = $this->attributeGateway->findBy(new Comparison('product.code', '=', $code));

        Assert::allKeyExists($result, 'id');
        Assert::allKeyExists($result, 'product_specification_id');
        Assert::allKeyExists($result, 'attribute_definition_id');
        Assert::allKeyExists($result, 'discriminator');
        Assert::allKeyExists($result, 'checkbox_id');
        Assert::allKeyExists($result, 'checkbox_value');
        Assert::allKeyExists($result, 'float_id');
        Assert::allKeyExists($result, 'float_value');
        Assert::allKeyExists($result, 'integer_id');
        Assert::allKeyExists($result, 'integer_value');
        Assert::allKeyExists($result, 'selection_id');
        Assert::allKeyExists($result, 'selection_value');
        Assert::allKeyExists($result, 'color_id');
        Assert::allKeyExists($result, 'color_value');

        return $result;
    }
}
