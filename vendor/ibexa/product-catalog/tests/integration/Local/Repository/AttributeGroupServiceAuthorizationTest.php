<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupQuery;

/**
 * @covers \Ibexa\ProductCatalog\Local\Repository\AttributeGroupService
 *
 * @group attribute-group-service
 */
final class AttributeGroupServiceAuthorizationTest extends BaseAttributeGroupServiceTest
{
    public function testGetAttributeGroupThrowsUnauthorizedException(): void
    {
        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'view\' \'product_type\'/');

        self::getAttributeGroupService()->getAttributeGroup('dimensions');
    }

    public function testFindAttributeGroupsThrowsUnauthorizedException(): void
    {
        self::setAnonymousUser();

        $attributeGroupList = self::getAttributeGroupService()->findAttributeGroups(new AttributeGroupQuery());

        self::assertEquals(0, $attributeGroupList->getTotalCount());
    }

    public function testCreateAttributeGroupThrowsUnauthorizedException(): void
    {
        $attributeGroupService = self::getLocalAttributeGroupService();

        $createStruct = $attributeGroupService->newAttributeGroupCreateStruct('custom');
        $createStruct->setNames([
            'eng-US' => 'Custom',
            'pol-PL' => 'Niestandardowa',
        ]);

        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'create\' \'product_type\'/');

        $attributeGroupService->createAttributeGroup($createStruct);
    }

    public function testDeleteAttributeGroupThrowsUnauthorizedException(): void
    {
        $attributeGroupService = self::getLocalAttributeGroupService();
        $attributeGroup = $attributeGroupService->getAttributeGroup('dimensions');

        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'delete\' \'product_type\'/');

        $attributeGroupService->deleteAttributeGroup($attributeGroup);
    }

    public function testUpdateAttributeGroupThrowsUnauthorizedException(): void
    {
        $attributeGroupService = self::getLocalAttributeGroupService();

        $attributeGroup = $attributeGroupService->getAttributeGroup('dimensions');

        $updateStruct = $attributeGroupService->newAttributeGroupUpdateStruct($attributeGroup);
        $updateStruct->setIdentifier('updated');
        $updateStruct->setNames([
            'eng-US' => 'Updated',
        ]);

        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'edit\' \'product_type\'/');

        $attributeGroupService->updateAttributeGroup($attributeGroup, $updateStruct);
    }

    public function testDeleteAttributeGroupTranslationThrowsUnauthorizedException(): void
    {
        $attributeGroupService = self::getLocalAttributeGroupService();
        $attributeGroup = $attributeGroupService->getAttributeGroup('dimensions');

        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'delete\' \'product_type\'/');

        $attributeGroupService->deleteAttributeGroupTranslation($attributeGroup, 'eng-US');
    }
}
