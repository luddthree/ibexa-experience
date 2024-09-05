<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Converter;

use Ibexa\Bundle\ProductCatalog\Converter\CatalogParamConverter;
use Ibexa\Contracts\ProductCatalog\CatalogServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use Ibexa\Tests\Bundle\Core\Converter\AbstractParamConverterTest;
use Symfony\Component\HttpFoundation\Request;

final class CatalogParamConverterTest extends AbstractParamConverterTest
{
    private const EXAMPLE_CATALOG_ID = 1;

    /** @var \Ibexa\Contracts\ProductCatalog\CatalogServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private CatalogServiceInterface $catalogService;

    private CatalogParamConverter $converter;

    protected function setUp(): void
    {
        $this->catalogService = $this->createMock(CatalogServiceInterface::class);
        $this->converter = new CatalogParamConverter($this->catalogService);
    }

    public function testSupports(): void
    {
        $config = $this->createConfiguration(CatalogInterface::class);
        self::assertTrue($this->converter->supports($config));

        $config = $this->createConfiguration(__CLASS__);
        self::assertFalse($this->converter->supports($config));

        $config = $this->createConfiguration();
        self::assertFalse($this->converter->supports($config));
    }

    public function testApplyCatalog(): void
    {
        $valueObject = $this->createMock(CatalogInterface::class);

        $this->catalogService
            ->method('getCatalog')
            ->with(self::EXAMPLE_CATALOG_ID)
            ->willReturn($valueObject);

        $request = new Request([], [], ['catalogId' => self::EXAMPLE_CATALOG_ID]);
        $config = $this->createConfiguration(CatalogInterface::class, 'catalog');

        $this->converter->apply($request, $config);

        self::assertInstanceOf(
            CatalogInterface::class,
            $request->attributes->get('catalog')
        );
    }
}
