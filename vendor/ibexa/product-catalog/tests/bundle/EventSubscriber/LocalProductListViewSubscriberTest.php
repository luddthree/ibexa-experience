<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Bundle\ProductCatalog\EventSubscriber\LocalProductListViewSubscriber;
use Ibexa\Bundle\ProductCatalog\View\ProductListView;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductListInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductType\ProductTypeListInterface;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Core\MVC\Symfony\Event\PreContentViewEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\RouterInterface;

final class LocalProductListViewSubscriberTest extends AbstractLocalViewSubscriberTest
{
    /**
     * @dataProvider dataProviderForOnPreContentView
     */
    public function testOnPreContentView(
        bool $missingAtLeastOneProductTypeDefinition,
        bool $missingAtLeastOneProductDefinition
    ): void {
        $view = new ProductListView(
            'example.html.twig',
            [],
            $this->createMock(FormInterface::class),
            $this->createMock(TaxonomyEntry::class),
            'sample-url'
        );

        $subscriber = new LocalProductListViewSubscriber(
            $this->createSiteAccessServiceMock(true),
            $this->createConfigProvider(true),
            $this->createProductTypeServiceMock($missingAtLeastOneProductTypeDefinition),
            $this->createProductServiceMock($missingAtLeastOneProductDefinition),
            $this->createFormFactoryMock(),
            $this->createMock(RouterInterface::class),
            $this->createMock(PermissionResolverInterface::class),
            $this->createMock(TaxonomyServiceInterface::class),
            'product-taxonomy'
        );

        $subscriber->onPreContentView(new PreContentViewEvent($view));

        self::assertTrue($view->isEditable());
        self::assertTrue($view->hasParameter('bulk_delete_form'));
        self::assertTrue($view->hasParameter('create_form'));
        self::assertTrue($view->hasParameter('no_products'));
        self::assertEquals(
            $missingAtLeastOneProductDefinition,
            $view->getParameter('no_products')
        );
        self::assertTrue($view->hasParameter('no_product_types'));
        self::assertEquals(
            $missingAtLeastOneProductTypeDefinition,
            $view->getParameter('no_product_types')
        );
        self::assertTrue($view->hasParameter('category_taxonomy'));
        self::assertTrue($view->hasParameter('category_path'));
    }

    /**
     * @return iterable<string,array{bool,bool}>
     */
    public function dataProviderForOnPreContentView(): iterable
    {
        yield 'missing at least one product type definition' => [true, false];
        yield 'missing at least one product definition' => [false, true];
        yield 'missing at least one product and product type definition' => [true, true];
        yield 'at least one product and product type are defined' => [false, false];
    }

    /**
     * @dataProvider dataProviderForTestOnPreContentViewIsNonApplicable
     */
    public function testOnPreContentViewIsNonApplicable(bool $isAdminSiteAccess, bool $isLocal): void
    {
        $view = new ProductListView(
            'example.html.twig',
            [],
            $this->createMock(FormInterface::class),
            $this->createMock(TaxonomyEntry::class),
            'sample-url'
        );

        $subscriber = new LocalProductListViewSubscriber(
            $this->createSiteAccessServiceMock($isAdminSiteAccess),
            $this->createConfigProvider($isLocal),
            $this->createProductTypeServiceMock(),
            $this->createProductServiceMock(),
            $this->createMock(FormFactoryInterface::class),
            $this->createMock(RouterInterface::class),
            $this->createMock(PermissionResolverInterface::class),
            $this->createMock(TaxonomyServiceInterface::class),
            'product-taxonomy'
        );

        $subscriber->onPreContentView(new PreContentViewEvent($view));

        self::assertFalse($view->isEditable());
        self::assertFalse($view->hasParameter('bulk_delete_form'));
        self::assertFalse($view->hasParameter('create_form'));
        self::assertFalse($view->hasParameter('no_products'));
        self::assertFalse($view->hasParameter('no_product_types'));
        self::assertFalse($view->hasParameter('category_taxonomy'));
        self::assertFalse($view->hasParameter('category_path'));
    }

    /**
     * @return iterable<string,array{bool,bool}>
     */
    public function dataProviderForTestOnPreContentViewIsNonApplicable(): iterable
    {
        yield 'non administrative siteaccess' => [false, true];
        yield 'read only product catalog' => [true, false];
    }

    private function createProductServiceMock(
        bool $withEmptyProductList = false
    ): ProductServiceInterface {
        $list = $this->createMock(ProductListInterface::class);
        $list->method('getTotalCount')->willReturn($withEmptyProductList ? 0 : 1);

        $productService = $this->createMock(ProductServiceInterface::class);
        $productService->method('findProducts')->willReturn($list);

        return $productService;
    }

    private function createProductTypeServiceMock(
        bool $withEmptyProductTypeList = false
    ): ProductTypeServiceInterface {
        $list = $this->createMock(ProductTypeListInterface::class);
        $list->method('getTotalCount')->willReturn($withEmptyProductTypeList ? 0 : 1);

        $productTypeService = $this->createMock(ProductTypeServiceInterface::class);
        $productTypeService->method('findProductTypes')->willReturn($list);

        return $productTypeService;
    }
}
