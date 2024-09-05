<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Persistence\Legacy\ProductTypeSettings;

use Ibexa\Contracts\Core\Persistence\Content\Type as ContentType;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductTypeSettings\HandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductTypeSettingCreateStruct;

/**
 * @covers \Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductTypeSettings\Handler
 */
final class HandlerTest extends AbstractProductTypeSettingTestCase
{
    private HandlerInterface $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = self::getServiceByClassName(HandlerInterface::class);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testCreate(): void
    {
        $fieldId = $this->getProductSpecificationFieldDefinitionId(
            $this->createProductType(
                self::VIRTUAL_PRODUCT_TYPE_IDENTIFIER,
                true
            )
        );

        $setting = current(
            $this->handler->findBy(
                ['field_definition_id' => $fieldId]
            )
        );

        self::assertNotFalse($setting);
        self::assertSame($fieldId, $setting->getFieldDefinitionId());
        self::assertTrue($setting->isVirtual());
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testPublish(): void
    {
        $fieldId = $this->getProductSpecificationFieldDefinitionId(
            $this->createProductType(self::VIRTUAL_PRODUCT_TYPE_IDENTIFIER)
        );

        $draftId = $this->handler->create(
            new ProductTypeSettingCreateStruct(
                $fieldId,
                ContentType::STATUS_DRAFT,
                true
            )
        );

        self::assertGreaterThan(0, $draftId);

        $this->handler->publish($fieldId);

        $draft = $this->handler->findBy(
            [
                'field_definition_id' => $fieldId,
                'status' => ContentType::STATUS_DRAFT,
            ]
        );

        self::assertCount(0, $draft);

        $result = $this->handler->findBy(
            [
                'field_definition_id' => $fieldId,
                'status' => ContentType::STATUS_DEFINED,
            ]
        );

        self::assertCount(1, $result);

        $setting = current($result);

        self::assertNotFalse($setting);

        self::assertSame($fieldId, $setting->getFieldDefinitionId());
        self::assertTrue($setting->isVirtual());
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testFindByFieldDefinitionId(): void
    {
        $fieldId = $this->getProductSpecificationFieldDefinitionId(
            $this->createProductType(
                self::VIRTUAL_PRODUCT_TYPE_IDENTIFIER,
                true
            )
        );

        $setting = $this->handler->findByFieldDefinitionId(
            $fieldId,
            ContentType::STATUS_DEFINED,
        );

        self::assertSame($fieldId, $setting->getFieldDefinitionId());
        self::assertTrue($setting->isVirtual());
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function testFindByFieldDefinitionIdThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);

        $this->handler->findByFieldDefinitionId(
            123456,
            ContentType::STATUS_DEFINED,
        );
    }

    public function testDeleteByFieldDefinitionId(): void
    {
        $fieldId = $this->getProductSpecificationFieldDefinitionId(
            $this->createProductType(self::VIRTUAL_PRODUCT_TYPE_IDENTIFIER)
        );

        $this->handler->deleteByFieldDefinitionId($fieldId, ContentType::STATUS_DEFINED);

        self::assertSame(
            0,
            $this->handler->countBy(
                ['field_definition_id' => $fieldId]
            )
        );
    }
}
