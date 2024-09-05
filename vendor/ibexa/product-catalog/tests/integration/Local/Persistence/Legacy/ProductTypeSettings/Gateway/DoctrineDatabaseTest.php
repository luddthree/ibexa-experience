<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Persistence\Legacy\ProductTypeSettings\Gateway;

use Ibexa\Contracts\Core\Persistence\Content\Type as ContentType;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductTypeSettings\GatewayInterface;
use Ibexa\Tests\Integration\ProductCatalog\Local\Persistence\Legacy\ProductTypeSettings\AbstractProductTypeSettingTestCase;

/**
 * @covers \Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductTypeSettings\Gateway\DoctrineDatabase
 */
final class DoctrineDatabaseTest extends AbstractProductTypeSettingTestCase
{
    private GatewayInterface $database;

    protected function setUp(): void
    {
        parent::setUp();

        $this->database = self::getServiceByClassName(GatewayInterface::class);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testInsert(): void
    {
        $fieldId = $this->getProductSpecificationFieldDefinitionId(
            $this->createProductType(
                self::VIRTUAL_PRODUCT_TYPE_IDENTIFIER,
                true
            )
        );

        $rows = $this->database->findBy(
            [
                'field_definition_id' => $fieldId,
                'status' => ContentType::STATUS_DEFINED,
            ]
        );

        self::assertCount(1, $rows);

        $result = current($rows);

        self::assertNotFalse($result);
        self::assertSame($fieldId, $result['field_definition_id']);
        self::assertSame(ContentType::STATUS_DEFINED, $result['status']);
        self::assertTrue($result['is_virtual']);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testUpdate(): void
    {
        $fieldId = $this->getProductSpecificationFieldDefinitionId(
            $this->createProductType(self::VIRTUAL_PRODUCT_TYPE_IDENTIFIER)
        );

        $this->database->update(
            [
                'status' => ContentType::STATUS_DEFINED,
                'is_virtual' => true,
            ],
            $fieldId,
            ContentType::STATUS_DEFINED
        );

        $result = $this->database->findBy(
            [
                'field_definition_id' => $fieldId,
                'status' => ContentType::STATUS_DEFINED,
            ]
        );

        self::assertCount(1, $result);

        $setting = current($result);

        self::assertNotFalse($setting);

        self::assertSame($fieldId, $setting['field_definition_id']);
        self::assertTrue($setting['is_virtual']);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testDeleteBy(): void
    {
        $fieldId = $this->getProductSpecificationFieldDefinitionId(
            $this->createProductType(self::VIRTUAL_PRODUCT_TYPE_IDENTIFIER)
        );

        $criteria = [
            'field_definition_id' => $fieldId,
            'status' => ContentType::STATUS_DEFINED,
        ];

        $this->database->deleteBy($criteria);

        self::assertEmpty($this->database->findBy($criteria));
    }
}
