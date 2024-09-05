<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Bundle\ProductCatalog\EventSubscriber\LocalProductTypeListViewSubscriber;
use Ibexa\Bundle\ProductCatalog\View\ProductTypeListView;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\ProductType\Create;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\ProductType\Delete;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductType\ProductTypeListInterface;
use Ibexa\Core\MVC\Symfony\Event\PreContentViewEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class LocalProductTypeListViewSubscriberTest extends AbstractLocalViewSubscriberTest
{
    /**
     * @dataProvider dataProviderForOnPreContentView
     */
    public function testOnPreContentView(
        bool $missingAtLeastOneProductTypeDefinition
    ): void {
        $view = new ProductTypeListView('example.html.twig', [], $this->createMock(FormInterface::class));

        $subscriber = new LocalProductTypeListViewSubscriber(
            $this->createSiteAccessServiceMock(true),
            $this->createConfigProvider(true),
            $this->createProductTypeServiceMock($missingAtLeastOneProductTypeDefinition),
            $this->createFormFactoryMock(),
            $this->createMock(UrlGeneratorInterface::class),
            $this->createPermissionResolverMock(true, true)
        );

        $subscriber->onPreContentView(new PreContentViewEvent($view));

        self::assertTrue($view->isEditable());
        self::assertTrue($view->hasParameter('bulk_delete_form'));
        self::assertTrue($view->hasParameter('create_form'));
        self::assertTrue($view->hasParameter('no_product_types'));
        self::assertEquals(
            $missingAtLeastOneProductTypeDefinition,
            $view->getParameter('no_product_types')
        );
    }

    /**
     * @return iterable<string,array{bool}>
     */
    public function dataProviderForOnPreContentView(): iterable
    {
        yield 'missing at least one product type definition' => [true];
        yield 'at least one product type is defined' => [false];
    }

    public function testOnPreContentViewWithoutDeletePermissions(): void
    {
        $view = new ProductTypeListView('example.html.twig', [], $this->createMock(FormInterface::class));

        $subscriber = new LocalProductTypeListViewSubscriber(
            $this->createSiteAccessServiceMock(true),
            $this->createConfigProvider(true),
            $this->createProductTypeServiceMock(),
            $this->createFormFactoryMock(),
            $this->createMock(UrlGeneratorInterface::class),
            $this->createPermissionResolverMock(true, false)
        );

        $subscriber->onPreContentView(new PreContentViewEvent($view));

        self::assertTrue($view->isEditable());
        self::assertFalse($view->hasParameter('bulk_delete_form'));
        self::assertTrue($view->hasParameter('no_product_types'));
    }

    public function testOnPreContentViewWithoutCreatePermissions(): void
    {
        $view = new ProductTypeListView('example.html.twig', [], $this->createMock(FormInterface::class));

        $subscriber = new LocalProductTypeListViewSubscriber(
            $this->createSiteAccessServiceMock(true),
            $this->createConfigProvider(true),
            $this->createProductTypeServiceMock(),
            $this->createFormFactoryMock(),
            $this->createMock(UrlGeneratorInterface::class),
            $this->createPermissionResolverMock(false, false)
        );

        $subscriber->onPreContentView(new PreContentViewEvent($view));

        self::assertTrue($view->isEditable());
        self::assertFalse($view->hasParameter('create_form'));
    }

    /**
     * @dataProvider dataProviderForTestOnPreContentViewIsNonApplicable
     */
    public function testOnPreContentViewIsNonApplicable(bool $isAdminSiteAccess, bool $isLocal): void
    {
        $view = new ProductTypeListView('example.html.twig', [], $this->createMock(FormInterface::class));

        $subscriber = new LocalProductTypeListViewSubscriber(
            $this->createSiteAccessServiceMock($isAdminSiteAccess),
            $this->createConfigProvider($isLocal),
            $this->createProductTypeServiceMock(),
            $this->createFormFactoryMock(),
            $this->createMock(UrlGeneratorInterface::class),
            $this->createMock(PermissionResolverInterface::class)
        );

        $subscriber->onPreContentView(new PreContentViewEvent($view));

        self::assertFalse($view->isEditable());
        self::assertFalse($view->hasParameter('bulk_delete_form'));
        self::assertFalse($view->hasParameter('no_product_types'));
    }

    /**
     * @return iterable<string,array{bool,bool}>
     */
    public function dataProviderForTestOnPreContentViewIsNonApplicable(): iterable
    {
        yield 'non administrative siteaccess' => [false, true];
        yield 'read only product catalog' => [true, false];
    }

    private function createPermissionResolverMock(bool $canCreate, bool $canDelete): PermissionResolverInterface
    {
        $permissionResolver = $this->createMock(PermissionResolverInterface::class);
        $permissionResolver
            ->method('canUser')
            ->willReturnCallback(
                static function (PolicyInterface $policy) use ($canCreate, $canDelete): bool {
                    if ($policy instanceof Create) {
                        return $canCreate;
                    }
                    if ($policy instanceof Delete) {
                        return $canDelete;
                    }

                    return false;
                }
            );

        return $permissionResolver;
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
