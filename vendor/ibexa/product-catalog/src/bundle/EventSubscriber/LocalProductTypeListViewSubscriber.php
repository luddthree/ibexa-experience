<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Bundle\ProductCatalog\Form\Type\ProductTypeBulkDeleteType;
use Ibexa\Bundle\ProductCatalog\Form\Type\ProductTypeCreateType;
use Ibexa\Bundle\ProductCatalog\View\ProductTypeListView;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\ProductType\Create;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\ProductType\Delete;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductType\ProductTypeQuery;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class LocalProductTypeListViewSubscriber extends AbstractLocalViewSubscriber
{
    private ProductTypeServiceInterface $productTypeService;

    private FormFactoryInterface $formFactory;

    private UrlGeneratorInterface $urlGenerator;

    private PermissionResolverInterface $permissionResolver;

    public function __construct(
        SiteAccessServiceInterface $siteAccessService,
        ConfigProviderInterface $configProvider,
        ProductTypeServiceInterface $productTypeService,
        FormFactoryInterface $formFactory,
        UrlGeneratorInterface $urlGenerator,
        PermissionResolverInterface $permissionResolver
    ) {
        parent::__construct($siteAccessService, $configProvider);

        $this->productTypeService = $productTypeService;
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
        $this->permissionResolver = $permissionResolver;
    }

    protected function supports(View $view): bool
    {
        return $view instanceof ProductTypeListView;
    }

    /**
     * @param \Ibexa\Bundle\ProductCatalog\View\ProductTypeListView $view
     */
    protected function configureView(View $view): void
    {
        $view->setEditable(true);
        $view->addParameters(['no_product_types' => $this->isMissingAtLeastOneProductType()]);

        if ($this->canCreateProductTypes()) {
            $view->addParameters([
                'create_form' => $this->createProductTypeCreateForm()->createView(),
            ]);
        }

        if ($this->canDeleteProductTypes()) {
            $view->addParameters([
                'bulk_delete_form' => $this->createBulkDeleteForm()->createView(),
            ]);
        }
    }

    private function createBulkDeleteForm(): FormInterface
    {
        return $this->formFactory->create(ProductTypeBulkDeleteType::class, null, [
            'method' => Request::METHOD_POST,
            'action' => $this->urlGenerator->generate('ibexa.product_catalog.product_type.bulk_delete'),
        ]);
    }

    private function createProductTypeCreateForm(): FormInterface
    {
        return $this->formFactory->create(ProductTypeCreateType::class, null, [
            'method' => Request::METHOD_POST,
            'action' => $this->urlGenerator->generate('ibexa.product_catalog.product_type.create'),
        ]);
    }

    private function canCreateProductTypes(): bool
    {
        return $this->permissionResolver->canUser(new Create());
    }

    private function canDeleteProductTypes(): bool
    {
        return $this->permissionResolver->canUser(new Delete());
    }

    private function isMissingAtLeastOneProductType(): bool
    {
        $query = new ProductTypeQuery();
        $query->setLimit(0);

        $productTypesList = $this->productTypeService->findProductTypes($query);

        return $productTypesList->getTotalCount() === 0;
    }
}
