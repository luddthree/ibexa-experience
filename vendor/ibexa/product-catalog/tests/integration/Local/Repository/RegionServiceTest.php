<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\RegionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Region\RegionQuery;
use Ibexa\Contracts\ProductCatalog\Values\RegionInterface;
use Ibexa\ProductCatalog\Local\Repository\RegionService;
use Ibexa\Tests\Integration\ProductCatalog\IbexaKernelTestCase;

/**
 * @covers \Ibexa\ProductCatalog\Local\Repository\RegionService
 */
final class RegionServiceTest extends IbexaKernelTestCase
{
    private const KNOWN_REGION_1 = '__REGION_1__';
    private const KNOWN_REGION_2 = '__REGION_2__';
    private const KNOWN_REGION_3 = '__REGION_3__';

    private const NON_EXISTENT_REGION = 'non-existing';

    private RegionService $regionService;

    protected function setUp(): void
    {
        self::bootKernel();

        $regionService = self::getServiceByClassName(RegionServiceInterface::class);
        assert($regionService instanceof RegionService);
        $this->regionService = $regionService;
    }

    /**
     * @dataProvider provideForGetRegions
     *
     * @param string[] $regionIdentifiers
     */
    public function testGetRegions(
        ?RegionQuery $query,
        int $expectedTotalCount,
        int $expectedCount,
        array $regionIdentifiers
    ): void {
        $regions = $this->regionService->findRegions($query);

        self::assertSame($expectedTotalCount, $regions->getTotalCount());
        self::assertCount($expectedCount, $regions);
        self::assertSame(
            $regionIdentifiers,
            array_values(
                array_map(
                    static fn (RegionInterface $region): string => $region->getIdentifier(),
                    $regions->getRegions(),
                ),
            ),
        );
    }

    /**
     * @phpstan-return iterable<array{
     *     \Ibexa\Contracts\ProductCatalog\Values\Region\RegionQuery|null,
     *     int,
     *     int,
     *     string[],
     * }>
     */
    public function provideForGetRegions(): iterable
    {
        yield [
            null,
            3,
            3,
            [
                self::KNOWN_REGION_1,
                self::KNOWN_REGION_2,
                self::KNOWN_REGION_3,
            ],
        ];

        yield [
            new RegionQuery(new FieldValueCriterion('identifier', self::KNOWN_REGION_1)),
            1,
            1,
            [
                self::KNOWN_REGION_1,
            ],
        ];

        yield [
            new RegionQuery(null, null, 2, 0),
            3,
            2,
            [
                self::KNOWN_REGION_1,
                self::KNOWN_REGION_2,
            ],
        ];

        yield [
            new RegionQuery(null, null, 2, 1),
            3,
            2,
            [
                self::KNOWN_REGION_2,
                self::KNOWN_REGION_3,
            ],
        ];
    }

    public function testGetRegion(): void
    {
        $region = $this->regionService->getRegion(self::KNOWN_REGION_1);

        self::assertSame(self::KNOWN_REGION_1, $region->getIdentifier());
    }

    public function testGetNonExistentRegionIn(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("Could not find 'Ibexa\Contracts\ProductCatalog\Values\RegionInterface' with identifier 'non-existing'");
        $this->regionService->getRegion(self::NON_EXISTENT_REGION);
    }
}
