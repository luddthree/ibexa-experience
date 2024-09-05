<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionQuery;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeType;

/**
 * @covers \Ibexa\ProductCatalog\Local\Repository\AttributeDefinitionService
 *
 * @group attribute-definition-service
 */
final class AttributeDefinitionServiceAuthorizationTest extends BaseAttributeDefinitionServiceTest
{
    private const ATTRIBUTE_DEFINITION_IDENTIFIER = 'foo';

    public function testGetAttributeDefinitionThrowsUnauthorizedException(): void
    {
        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'view\' \'product_type\'/');

        self::getAttributeDefinitionService()->getAttributeDefinition(self::ATTRIBUTE_DEFINITION_IDENTIFIER);
    }

    public function testFindAttributesDefinitionsThrowsUnauthorizedException(): void
    {
        self::setAnonymousUser();

        $attributesDefinitionList = self::getAttributeDefinitionService()
            ->findAttributesDefinitions(new AttributeDefinitionQuery());

        self::assertEquals(0, $attributesDefinitionList->getTotalCount());
    }

    public function testCreateAttributeDefinitionThrowsUnauthorizedException(): void
    {
        $attributeDefinitionService = self::getLocalAttributeDefinitionService();

        $createStruct = $attributeDefinitionService->newAttributeDefinitionCreateStruct('custom');
        $createStruct->setName('eng-US', 'Custom');
        $createStruct->setDescription('eng-US', 'Custom description');
        $createStruct->setGroup($this->getAttributeGroup());
        $createStruct->setType(new AttributeType('integer'));

        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'create\' \'product_type\'/');

        $attributeDefinitionService->createAttributeDefinition($createStruct);
    }

    public function testDeleteAttributeDefinitionThrowsUnauthorizedException(): void
    {
        $attributeDefinitionService = self::getLocalAttributeDefinitionService();
        $attributeDefinition = $attributeDefinitionService->getAttributeDefinition(self::ATTRIBUTE_DEFINITION_IDENTIFIER);

        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'delete\' \'product_type\'/');

        $attributeDefinitionService->deleteAttributeDefinition($attributeDefinition);
    }

    public function testUpdateAttributeDefinitionThrowsUnauthorizedException(): void
    {
        $attributeDefinitionService = self::getLocalAttributeDefinitionService();

        $attributeDefinition = $attributeDefinitionService->getAttributeDefinition(self::ATTRIBUTE_DEFINITION_IDENTIFIER);

        $updateStruct = $this->getUpdateStruct($attributeDefinition);

        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'edit\' \'product_type\'/');

        $attributeDefinitionService->updateAttributeDefinition($attributeDefinition, $updateStruct);
    }

    public function testDeleteAttributeDefinitionTranslationThrowsUnauthorizedException(): void
    {
        $attributeDefinitionService = self::getLocalAttributeDefinitionService();
        $attributeDefinition = $attributeDefinitionService->getAttributeDefinition(self::ATTRIBUTE_DEFINITION_IDENTIFIER);

        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'delete\' \'product_type\'/');

        $attributeDefinitionService->deleteAttributeDefinitionTranslation($attributeDefinition, 'eng-US');
    }
}
