<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Bundle\ProductCatalog\Form\Type\ProductBulkDeleteType;
use Ibexa\Bundle\ProductCatalog\Form\Type\ProductCreateRedirectType;
use Ibexa\Bundle\ProductCatalog\View\ProductListView;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Product\Edit;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\ProductType\ProductTypeQuery;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

final class LocalProductListViewSubscriber extends AbstractLocalViewSubscriber
{
    private ProductTypeServiceInterface $productTypeService;

    private ProductServiceInterface $productService;

    private FormFactoryInterface $formFactory;

    private RouterInterface $router;

    private PermissionResolverInterface $permissionResolver;

    private TaxonomyServiceInterface $taxonomyService;

    private string $productTaxonomyName;

    public function __construct(
        SiteAccessServiceInterface $siteAccessService,
        ConfigProviderInterface $configProvider,
        ProductTypeServiceInterface $productTypeService,
        ProductServiceInterface $productService,
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        PermissionResolverInterface $permissionResolver,
        TaxonomyServiceInterface $taxonomyService,
        string $productTaxonomyName
    ) {
        parent::__construct($siteAccessService, $configProvider);

        $this->productTypeService = $productTypeService;
        $this->productService = $productService;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->permissionResolver = $permissionResolver;
        $this->taxonomyService = $taxonomyService;
        $this->productTaxonomyName = $productTaxonomyName;
    }

    /**
     * @param \Ibexa\Bundle\ProductCatalog\View\ProductListView $view
     */
    protected function configureView(View $view): void
    {
        $category = $view->getCategory();

        $view->setEditable(true);
        $view->addParameters([
            'bulk_delete_form' => $this->createBulkDeleteForm()->createView(),
            'can_edit' => $this->canEdit(),
            'create_form' => $this->createCreateForm()->createView(),
            'no_products' => $this->isMissingAtLeastOneProduct(),
            'no_product_types' => $this->isMissingAtLeastOneProductType(),
            'category_taxonomy' => $this->productTaxonomyName,
            'category_path' => $this->getCategoryPath($category),
        ]);
    }

    protected function supports(View $view): bool
    {
        return $view instanceof ProductListView;
    }

    private function createCreateForm(): FormInterface
    {
        return $this->formFactory->create(ProductCreateRedirectType::class, null, [
            'method' => Request::METHOD_POST,
            'action' => $this->router->generate('ibexa.product_catalog.product.create_proxy'),
        ]);
    }

    private function createBulkDeleteForm(): FormInterface
    {
        return $this->formFactory->create(ProductBulkDeleteType::class, null, [
            'method' => Request::METHOD_POST,
            'action' => $this->router->generate('ibexa.product_catalog.product.bulk_delete'),
        ]);
    }

    private function canEdit(): bool
    {
        return $this->permissionResolver->canUser(new Edit());
    }

    private function isMissingAtLeastOneProduct(): bool
    {
        $query = new ProductQuery();
        $query->setLimit(0);

        $productList = $this->productService->findProducts($query);

        return $productList->getTotalCount() === 0;
    }

    private function isMissingAtLeastOneProductType(): bool
    {
        $query = new ProductTypeQuery();
        $query->setLimit(0);

        $productTypesList = $this->productTypeService->findProductTypes($query);

        return $productTypesList->getTotalCount() === 0;
    }

    /**
     * @return iterable<\Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry>
     */
    private function getCategoryPath(?TaxonomyEntry $category): iterable
    {
        if ($category) {
            return $this->taxonomyService->getPath($category);
        }

        $categoryRoot = $this->taxonomyService->loadRootEntry($this->productTaxonomyName);

        return $this->taxonomyService->getPath($categoryRoot);
    }
}
