<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogCopyStruct;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogDeleteTranslationStruct;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogUpdateStruct;
use Ibexa\ProductCatalog\Local\Repository\Values\Catalog\Catalog;

/**
 * @covers \Ibexa\ProductCatalog\Local\Repository\CatalogService
 *
 * @group catalog-service
 */
final class CatalogServiceAuthorizationTest extends BaseCatalogServiceTest
{
    public function testCreateThrowsUnauthorizedException(): void
    {
        $struct = $this->getCreateStruct();

        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'create\' \'catalog\'/');

        self::getCatalogService()->createCatalog($struct);
    }

    public function testFindThrowsThrowsUnauthorizedException(): void
    {
        self::setAnonymousUser();

        $catalogList = self::getCatalogService()->findCatalogs();

        self::assertCount(0, $catalogList);
    }

    public function testFindByIdentifierThrowsUnauthorizedException(): void
    {
        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'view\' \'catalog\'/');

        self::getCatalogService()->getCatalogByIdentifier(self::CATALOG_IDENTIFIER);
    }

    public function testGetCatalogThrowsUnauthorizedException(): void
    {
        $catalog = $this->createCatalog();
        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'view\' \'catalog\'/');

        self::getCatalogService()->getCatalog($catalog->getId());
    }

    public function testUpdateThrowsUnauthorizedException(): void
    {
        $catalog = $this->createCatalog();
        $updateStruct = new CatalogUpdateStruct($catalog->getId());

        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'edit\' \'catalog\'/');

        self::getCatalogService()->updateCatalog($catalog, $updateStruct);
    }

    public function testDeleteThrowsUnauthorizedException(): void
    {
        $catalog = $this->createCatalog();
        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'delete\' \'catalog\'/');

        self::getCatalogService()->deleteCatalog($catalog);
    }

    public function testCopyThrowsUnauthorizedException(): void
    {
        $catalog = $this->createCatalog();
        $copyStruct = new CatalogCopyStruct(
            $catalog->getId(),
            $catalog->getIdentifier()
        );

        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'create\' \'catalog\'/');

        self::getCatalogService()->copyCatalog($catalog, $copyStruct);
    }

    public function testDeleteTranslationThrowsUnauthorizedException(): void
    {
        $deleteTranslationStruct = new CatalogDeleteTranslationStruct(
            $this->createCatalog(),
            'pol-PL'
        );
        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'delete\' \'catalog\'/');

        self::getCatalogService()->deleteCatalogTranslation($deleteTranslationStruct);
    }
}
