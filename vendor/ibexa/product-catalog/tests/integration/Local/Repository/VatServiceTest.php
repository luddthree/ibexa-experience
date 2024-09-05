<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\ProductCatalog\RegionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\RegionInterface;
use Ibexa\Contracts\ProductCatalog\Values\VatCategoryInterface;
use Ibexa\Contracts\ProductCatalog\VatServiceInterface;
use Ibexa\ProductCatalog\Local\Repository\VatService;
use Ibexa\Tests\Integration\ProductCatalog\IbexaKernelTestCase;

/**
 * @covers \Ibexa\ProductCatalog\Local\Repository\VatService
 */
final class VatServiceTest extends IbexaKernelTestCase
{
    private VatService $vatService;

    private RegionInterface $region1;

    private RegionInterface $region2;

    protected function setUp(): void
    {
        self::bootKernel();

        $vatService = self::getServiceByClassName(VatServiceInterface::class);
        assert($vatService instanceof VatService);
        $this->vatService = $vatService;

        $regionService = self::getServiceByClassName(RegionServiceInterface::class);
        $this->region1 = $regionService->getRegion('__REGION_1__');
        $this->region2 = $regionService->getRegion('__REGION_2__');
    }

    public function testGetVatCategories(): void
    {
        $vatCategories = $this->vatService->getVatCategories($this->region1);
        self::assertCount(3, $vatCategories);
        [
            $vatCategory1,
            $vatCategory2,
            $vatCategory3,
        ] = $vatCategories->getVatCategories();

        $this->assertVatCategorySettings($vatCategory1, 'fii', '__REGION_1__', 12.0);
        $this->assertVatCategorySettings($vatCategory2, 'foo', '__REGION_1__', 0.0);
        $this->assertVatCategorySettings($vatCategory3, 'bar', '__REGION_1__', 0.0);

        $vatCategories = $this->vatService->getVatCategories($this->region2);
        self::assertCount(3, $vatCategories, 'Empty value should be replaced by root settings for region');

        [
            $vatCategory1,
            $vatCategory2,
            $vatCategory3,
        ] = $vatCategories->getVatCategories();

        $this->assertVatCategorySettings($vatCategory1, 'fii', '__REGION_2__', 24.0);
        $this->assertVatCategorySettings($vatCategory2, 'foo', '__REGION_2__', 0.0);
        $this->assertVatCategorySettings($vatCategory3, 'bar', '__REGION_2__', 0.0);
    }

    public function testGetVatCategoryByName(): void
    {
        $vatCategory = $this->vatService->getVatCategoryByIdentifier($this->region1, 'fii');
        $this->assertVatCategorySettings($vatCategory, 'fii', '__REGION_1__', 12.0);

        $vatCategory = $this->vatService->getVatCategoryByIdentifier($this->region1, 'foo');
        $this->assertVatCategorySettings($vatCategory, 'foo', '__REGION_1__', 0.0);

        $vatCategory = $this->vatService->getVatCategoryByIdentifier($this->region1, 'bar');
        $this->assertVatCategorySettings($vatCategory, 'bar', '__REGION_1__', 0.0);
    }

    private function assertVatCategorySettings(
        VatCategoryInterface $vatCategory,
        string $identifier,
        string $region,
        ?float $value
    ): void {
        self::assertSame($identifier, $vatCategory->getIdentifier());
        self::assertSame($region, $vatCategory->getRegion());
        self::assertSame($value, $vatCategory->getVatValue());
    }
}
