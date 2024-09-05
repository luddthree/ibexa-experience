<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\FormMapper;

use Ibexa\Bundle\ProductCatalog\Form\FormMapper\CatalogMapper;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\ProductCatalog\CatalogServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalAnd;
use Ibexa\ProductCatalog\Local\Repository\Values\Catalog\Catalog;
use PHPUnit\Framework\TestCase;

final class CatalogMapperTest extends TestCase
{
    private const CATALOG_ID = 14;
    private const CATALOG_IDENTIFIER = 'foo';

    /** @var \Ibexa\Contracts\ProductCatalog\CatalogServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private CatalogServiceInterface $catalogService;

    private CatalogMapper $catalogMapper;

    private Catalog $catalog;

    protected function setUp(): void
    {
        $this->catalog = $this->getCatalog();
        $this->catalogService = $this->createMock(CatalogServiceInterface::class);
        $this->catalogMapper = new CatalogMapper($this->catalogService);
    }

    public function testMapToFormData(): void
    {
        $language = new Language(['languageCode' => 'eng-GB']);
        $baseLanguage = new Language(['languageCode' => 'pol-PL']);
        $this->catalogService
            ->method('getCatalog')
            ->with(self::CATALOG_ID, [$baseLanguage->languageCode])
            ->willReturn($this->getCatalog());

        $catalogUpdateData = $this->catalogMapper->mapToFormData(
            $this->catalog,
            [
                'language' => $language,
                'baseLanguage' => $baseLanguage,
            ]
        );

        self::assertSame(self::CATALOG_ID, $catalogUpdateData->getId());
        self::assertSame(self::CATALOG_IDENTIFIER, $catalogUpdateData->getIdentifier());
        self::assertSame('name', $catalogUpdateData->getName());
        self::assertSame('description', $catalogUpdateData->getDescription());
        $language = $catalogUpdateData->getLanguage();
        self::assertSame('eng-GB', $language->languageCode);
    }

    public function testMapToFormDataWithoutBaseLanguage(): void
    {
        $language = new Language(['languageCode' => 'eng-GB']);
        $this->catalogService
            ->method('getCatalog')
            ->with(self::CATALOG_ID, [$language->languageCode])
            ->willReturn($this->getCatalog());

        $catalogUpdateData = $this->catalogMapper->mapToFormData(
            $this->catalog,
            [
                'language' => $language,
                'baseLanguage' => null,
            ]
        );

        self::assertSame(self::CATALOG_ID, $catalogUpdateData->getId());
        self::assertSame(self::CATALOG_IDENTIFIER, $catalogUpdateData->getIdentifier());
        self::assertSame('name', $catalogUpdateData->getName());
        self::assertSame('description', $catalogUpdateData->getDescription());
        $language = $catalogUpdateData->getLanguage();
        self::assertSame('eng-GB', $language->languageCode);
    }

    private function getTestUser(): User
    {
        return $this->createMock(User::class);
    }

    private function getCatalog(): Catalog
    {
        return new Catalog(
            self::CATALOG_ID,
            self::CATALOG_IDENTIFIER,
            'name',
            ['pol-PL'],
            $this->getTestUser(),
            1647865210,
            1647865251,
            'new',
            new LogicalAnd([]),
            'description',
        );
    }
}
