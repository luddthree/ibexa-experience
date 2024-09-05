<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogCopyStruct;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogDeleteTranslationStruct;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductType;

/**
 * @covers \Ibexa\ProductCatalog\Local\Repository\CatalogService
 *
 * @group catalog-service
 */
final class CatalogServiceTest extends BaseCatalogServiceTest
{
    private const UPDATED_NAME = 'updated name';
    private const UPDATED_DESCRIPTION = 'updated description';

    public function testCreateCatalog(): void
    {
        $catalog = $this->createCatalog();

        self::assertCatalog($catalog);
    }

    public function testCreateCatalogWithoutDescription(): void
    {
        $catalog = $this->createCatalog(
            self::CATALOG_IDENTIFIER,
            [
                self::ENG_US => self::ENG_US_CATALOG_NAME,
            ],
            [/* No description */]
        );

        self::assertCatalog(
            $catalog,
            null,
            self::CATALOG_IDENTIFIER,
            self::ENG_US_CATALOG_NAME,
            '' /* Empty description */
        );
    }

    public function testCreateCatalogWithSingleCriterion(): void
    {
        $catalog = $this->createCatalog(
            self::CATALOG_IDENTIFIER,
            [
                self::ENG_US => self::ENG_US_CATALOG_NAME,
            ],
            [self::ENG_US => self::ENG_US_CATALOG_DESCRIPTION],
            new ProductType(['identifier'])
        );

        self::assertCatalog(
            $catalog,
            null,
            self::CATALOG_IDENTIFIER,
            self::ENG_US_CATALOG_NAME,
            self::ENG_US_CATALOG_DESCRIPTION,
            self::CATALOG_STATUS_DRAFT,
            self::ADMIN_ID,
            new LogicalAnd([new ProductType(['identifier'])])
        );
    }

    public function testUpdateCatalog(): void
    {
        $catalog = $this->createCatalog();

        $updateStruct = new CatalogUpdateStruct(
            $catalog->getId(),
            'publish',
            'bar',
            new LogicalAnd([]),
            [self::ENG_US => self::UPDATED_NAME],
            [self::ENG_US => self::UPDATED_DESCRIPTION]
        );

        $updatedCatalog = self::getCatalogService()->updateCatalog($catalog, $updateStruct);

        self::assertCatalog(
            $updatedCatalog,
            $catalog->getId(),
            'bar',
            self::UPDATED_NAME,
            self::UPDATED_DESCRIPTION,
            self::CATALOG_STATUS_PUBLISHED
        );
    }

    public function testUpdateCatalogWithoutChangingStatus(): void
    {
        $catalog = $this->createCatalog();

        $updateStruct = new CatalogUpdateStruct(
            $catalog->getId(),
            null,
            'bar',
            new LogicalAnd([]),
            [self::ENG_US => self::UPDATED_NAME],
            [self::ENG_US => self::UPDATED_DESCRIPTION]
        );

        $updatedCatalog = self::getCatalogService()->updateCatalog($catalog, $updateStruct);

        self::assertCatalog(
            $updatedCatalog,
            $catalog->getId(),
            'bar',
            self::UPDATED_NAME,
            self::UPDATED_DESCRIPTION,
            self::CATALOG_STATUS_DRAFT
        );
    }

    /**
     * @dataProvider dataProviderForUnsupportedTransition
     */
    public function testUpdateCatalogWithUnsupportedTransition(string $transition): void
    {
        $catalog = $this->createCatalog();

        $updateStruct = new CatalogUpdateStruct(
            $catalog->getId(),
            $transition,
            'bar',
            new LogicalAnd([]),
            [self::ENG_US => self::UPDATED_NAME],
            [self::ENG_US => self::UPDATED_DESCRIPTION]
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unable to perform transition: ' . $transition);

        self::getCatalogService()->updateCatalog($catalog, $updateStruct);
    }

    /**
     * @return iterable<array{string}>
     */
    public function dataProviderForUnsupportedTransition(): iterable
    {
        yield 'nonexistent' => ['nonexistent'];

        yield 'blocked transition' => ['archive'];
    }

    public function testGetCatalogByIdentifier(): void
    {
        $catalog = $this->createCatalog();
        $catalogByIdentifier = self::getCatalogService()->getCatalogByIdentifier(
            $catalog->getIdentifier()
        );

        self::assertCatalog($catalogByIdentifier, $catalog->getId());
    }

    public function testDeleteCatalog(): void
    {
        $catalog = $this->createCatalog();

        self::getCatalogService()->deleteCatalog($catalog);

        self::assertCatalogIsDeleted($catalog);
    }

    public function testGetCatalog(): void
    {
        $catalog = $this->createCatalog();
        $catalogById = self::getCatalogService()->getCatalog(
            $catalog->getId()
        );

        self::assertCatalog($catalogById, $catalog->getId());
    }

    public function testFindCatalogs(): void
    {
        $catalogA = $this->createCatalog();
        $catalogB = $this->createCatalog(
            'bar',
            [self::ENG_US => 'nameB'],
            [self::ENG_US => 'descriptionB']
        );
        $catalogC = $this->createCatalog(
            'baz',
            [self::ENG_US => 'nameC'],
            [self::ENG_US => 'descriptionC']
        );
        $catalogList = self::getCatalogService()->findCatalogs();

        self::assertCount(3, $catalogList);
        $catalogs = $catalogList->getCatalogs();
        self::assertEquals(
            [
                $catalogA,
                $catalogB,
                $catalogC,
            ],
            $catalogs
        );
    }

    public function testCopyCatalog(): void
    {
        $catalog = $this->createCatalog();
        $copyStruct = new CatalogCopyStruct(
            $catalog->getId(),
            'newIdentifier'
        );

        $copiedCatalog = self::getCatalogService()->copyCatalog($catalog, $copyStruct);

        self::assertCatalog(
            $copiedCatalog,
            null,
            'newIdentifier',
            $catalog->getName(),
            $catalog->getDescription(),
            $catalog->getStatus()
        );
    }

    public function testDeleteCatalogTranslation(): void
    {
        $catalogService = self::getCatalogService();
        $languageService = self::getLanguageService();
        $translationLanguage = $languageService->loadLanguage('fre-FR');
        $catalog = $this->createCatalog();
        $catalogId = $catalog->getId();
        $updateStruct = new CatalogUpdateStruct(
            $catalog->getId(),
            'publish',
            'bar',
            new LogicalAnd([]),
            [
                $translationLanguage->languageCode => 'la traduction',
            ],
            [
                $translationLanguage->languageCode => 'traduction du descriptif',
            ]
        );

        $catalogService->updateCatalog($catalog, $updateStruct);
        $catalog = $catalogService->getCatalog(
            $catalogId,
            [$translationLanguage->languageCode]
        );

        self::assertSame('la traduction', $catalog->getName());
        self::assertSame('traduction du descriptif', $catalog->getDescription());
        $deleteTranslationStruct = new CatalogDeleteTranslationStruct(
            $catalog,
            $translationLanguage->languageCode
        );
        $catalogService->deleteCatalogTranslation(
            $deleteTranslationStruct
        );
        $catalog = $catalogService->getCatalog(
            $catalogId,
            [$translationLanguage->languageCode]
        );

        self::assertSame('', $catalog->getName());
    }
}
