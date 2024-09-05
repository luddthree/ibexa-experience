<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Converter;

use Ibexa\Bundle\ProductCatalog\Converter\ProductTypeParamConverter;
use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\Tests\Bundle\Core\Converter\AbstractParamConverterTest;
use Symfony\Component\HttpFoundation\Request;

final class ProductTypeParamConverterTest extends AbstractParamConverterTest
{
    private const EXAMPLE_PRODUCT_TYPE_IDENTIFIER = 'dress';

    /** @var \Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ProductTypeServiceInterface $productTypeService;

    private ProductTypeParamConverter $converter;

    protected function setUp(): void
    {
        $this->productTypeService = $this->createMock(ProductTypeServiceInterface::class);
        $this->converter = new ProductTypeParamConverter($this->productTypeService);
    }

    public function testSupports(): void
    {
        $config = $this->createConfiguration(ProductTypeInterface::class);
        self::assertTrue($this->converter->supports($config));

        $config = $this->createConfiguration(__CLASS__);
        self::assertFalse($this->converter->supports($config));

        $config = $this->createConfiguration();
        self::assertFalse($this->converter->supports($config));
    }

    public function testApplyProductType(): void
    {
        $valueObject = $this->createMock(ProductTypeInterface::class);

        $this->productTypeService
            ->method('getProductType')
            ->with(self::EXAMPLE_PRODUCT_TYPE_IDENTIFIER)
            ->willReturn($valueObject);

        $request = new Request([], [], ['productTypeIdentifier' => self::EXAMPLE_PRODUCT_TYPE_IDENTIFIER]);
        $config = $this->createConfiguration(ProductTypeInterface::class, 'productType');

        $this->converter->apply($request, $config);

        self::assertInstanceOf(
            ProductTypeInterface::class,
            $request->attributes->get('productType')
        );
    }
}
