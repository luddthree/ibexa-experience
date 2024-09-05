<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Bundle\ProductCatalog\Form\Type\Catalog\CatalogBulkDeleteType;
use Ibexa\Bundle\ProductCatalog\Form\Type\Catalog\CatalogCopyType;
use Ibexa\Bundle\ProductCatalog\View\CatalogListView;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Catalog\Create;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Catalog\Edit;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

final class LocalCatalogListViewSubscriber extends AbstractLocalViewSubscriber
{
    private PermissionResolverInterface $permissionResolver;

    private FormFactoryInterface $formFactory;

    private RouterInterface $router;

    public function __construct(
        SiteAccessServiceInterface $siteAccessService,
        ConfigProviderInterface $configProvider,
        PermissionResolverInterface $permissionResolver,
        FormFactoryInterface $formFactory,
        RouterInterface $router
    ) {
        parent::__construct($siteAccessService, $configProvider);
        $this->permissionResolver = $permissionResolver;
        $this->formFactory = $formFactory;
        $this->router = $router;
    }

    /**
     * @param \Ibexa\Bundle\ProductCatalog\View\CatalogListView $view
     */
    protected function configureView(View $view): void
    {
        $view->addParameters([
            'can_create' => $this->canCreate(),
            'can_edit' => $this->canEdit(),
            'bulk_delete_form' => $this->createBulkDeleteForm()->createView(),
            'copy_form' => $this->createCopyForm()->createView(),
        ]);
    }

    protected function isLocal(): bool
    {
        return true;
    }

    protected function supports(View $view): bool
    {
        return $view instanceof CatalogListView;
    }

    private function canCreate(): bool
    {
        return $this->permissionResolver->canUser(new Create());
    }

    private function canEdit(): bool
    {
        return $this->permissionResolver->canUser(new Edit());
    }

    private function createBulkDeleteForm(): FormInterface
    {
        return $this->formFactory->create(CatalogBulkDeleteType::class, null, [
            'method' => Request::METHOD_POST,
            'action' => $this->router->generate('ibexa.product_catalog.catalog.bulk_delete'),
        ]);
    }

    private function createCopyForm(): FormInterface
    {
        return $this->formFactory->create(
            CatalogCopyType::class,
            null,
            [
                'method' => Request::METHOD_POST,
                'action' => $this->router->generate('ibexa.product_catalog.catalog.copy'),
            ]
        );
    }
}
