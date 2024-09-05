<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Catalog\Gateway\DoctrineDatabase;
use Ibexa\Tests\Integration\ProductCatalog\IbexaKernelTestCase;
use Symfony\Bridge\PhpUnit\ClockMock;

abstract class BaseCatalogServiceTest extends IbexaKernelTestCase
{
    protected const ENG_US = 'eng-US';
    protected const CATALOG_ID = 1;
    protected const CATALOG_IDENTIFIER = 'identifier';
    protected const CATALOG_STATUS_DRAFT = 'draft';
    protected const CATALOG_STATUS_PUBLISHED = 'published';
    protected const ENG_US_CATALOG_NAME = 'name';
    protected const ENG_US_CATALOG_DESCRIPTION = 'description';
    protected const ADMIN_ID = 14;

    protected function setUp(): void
    {
        self::bootKernel();
        self::getLanguageResolver()->setContextLanguage(self::ENG_US);
        self::setAdministratorUser();
        ClockMock::register(DoctrineDatabase::class);
        ClockMock::withClockMock(true);
    }

    protected function tearDown(): void
    {
        ClockMock::withClockMock(false);
    }

    /**
     * @param array<string, string> $names
     * @param array<string, string> $descriptions
     */
    protected function createCatalog(
        string $identifier = self::CATALOG_IDENTIFIER,
        array $names = [self::ENG_US => self::ENG_US_CATALOG_NAME],
        array $descriptions = [self::ENG_US => self::ENG_US_CATALOG_DESCRIPTION],
        CriterionInterface $criterion = null
    ): CatalogInterface {
        $createStruct = $this->getCreateStruct(
            $identifier,
            $names,
            $descriptions,
            $criterion
        );

        return self::getCatalogService()->createCatalog($createStruct);
    }

    /**
     * @param array<string, string> $names
     * @param array<string, string> $descriptions
     */
    protected function getCreateStruct(
        string $identifier = self::CATALOG_IDENTIFIER,
        array $names = [self::ENG_US => self::ENG_US_CATALOG_NAME],
        array $descriptions = [self::ENG_US => self::ENG_US_CATALOG_DESCRIPTION],
        CriterionInterface $criterion = null
    ): CatalogCreateStruct {
        return new CatalogCreateStruct(
            $identifier,
            $criterion ?? new LogicalAnd([]),
            $names,
            $descriptions,
        );
    }

    protected static function assertCatalog(
        CatalogInterface $catalog,
        ?int $expectedId = null,
        ?string $expectedIdentifier = self::CATALOG_IDENTIFIER,
        string $expectedName = self::ENG_US_CATALOG_NAME,
        ?string $expectedDescription = self::ENG_US_CATALOG_DESCRIPTION,
        ?string $expectedStatus = self::CATALOG_STATUS_DRAFT,
        int $expectedCreatorId = self::ADMIN_ID,
        CriterionInterface $expectedCriterion = null
    ): void {
        if ($expectedId !== null) {
            self::assertSame($catalog->getId(), $catalog->getId());
        }
        $expectedCriterion = $expectedCriterion ?? new LogicalAnd([]);
        self::assertSame($expectedIdentifier, $catalog->getIdentifier());
        self::assertSame($expectedName, $catalog->getName());
        self::assertSame($expectedDescription, $catalog->getDescription());
        self::assertSame($expectedStatus, $catalog->getStatus());
        self::assertSame($expectedCreatorId, $catalog->getCreator()->getUserId());
        self::assertSame(ClockMock::time(), $catalog->getCreated());
        self::assertSame(ClockMock::time(), $catalog->getModified());
        self::assertEquals($expectedCriterion, $catalog->getQuery());
    }

    protected static function assertCatalogIsDeleted(CatalogInterface $catalog): void
    {
        try {
            self::getCatalogService()->getCatalogByIdentifier($catalog->getIdentifier());
            $isDeleted = false;
        } catch (NotFoundException $e) {
            $isDeleted = true;
        }

        self::assertTrue($isDeleted);
    }
}
