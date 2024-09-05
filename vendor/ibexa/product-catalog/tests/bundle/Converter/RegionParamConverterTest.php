<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Converter;

use Ibexa\Bundle\ProductCatalog\Converter\RegionParamConverter;
use Ibexa\Contracts\ProductCatalog\RegionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\RegionInterface;
use Ibexa\Tests\Bundle\Core\Converter\AbstractParamConverterTest;
use Symfony\Component\HttpFoundation\Request;

final class RegionParamConverterTest extends AbstractParamConverterTest
{
    private const EXAMPLE_REGION_IDENTIFIER = 'region';

    /** @var \Ibexa\Contracts\ProductCatalog\RegionServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private RegionServiceInterface $regionService;

    private RegionParamConverter $converter;

    protected function setUp(): void
    {
        $this->regionService = $this->createMock(RegionServiceInterface::class);
        $this->converter = new RegionParamConverter($this->regionService);
    }

    public function testSupports(): void
    {
        $config = $this->createConfiguration(RegionInterface::class);
        self::assertTrue($this->converter->supports($config));

        $config = $this->createConfiguration(__CLASS__);
        self::assertFalse($this->converter->supports($config));

        $config = $this->createConfiguration();
        self::assertFalse($this->converter->supports($config));
    }

    public function testApplyRegion(): void
    {
        $valueObject = $this->createMock(RegionInterface::class);

        $this->regionService
            ->method('getRegion')
            ->with(self::EXAMPLE_REGION_IDENTIFIER)
            ->willReturn($valueObject);

        $request = new Request([], [], ['regionIdentifier' => self::EXAMPLE_REGION_IDENTIFIER]);
        $config = $this->createConfiguration(RegionInterface::class, 'region');

        $this->converter->apply($request, $config);

        self::assertInstanceOf(
            RegionInterface::class,
            $request->attributes->get('region')
        );
    }
}
