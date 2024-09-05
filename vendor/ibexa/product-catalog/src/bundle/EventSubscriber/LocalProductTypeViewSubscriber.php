<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Bundle\ProductCatalog\Form\Data\ProductTypeDeleteData;
use Ibexa\Bundle\ProductCatalog\Form\Type\ProductTypeDeleteType;
use Ibexa\Bundle\ProductCatalog\View\ProductTypeView;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\ProductType\Delete;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class LocalProductTypeViewSubscriber extends AbstractLocalViewSubscriber
{
    private FormFactoryInterface $formFactory;

    private UrlGeneratorInterface $urlGenerator;

    private PermissionResolverInterface $permissionResolver;

    public function __construct(
        SiteAccessServiceInterface $siteAccessService,
        ConfigProviderInterface $configProvider,
        PermissionResolverInterface $permissionResolver,
        FormFactoryInterface $formFactory,
        UrlGeneratorInterface $urlGenerator
    ) {
        parent::__construct($siteAccessService, $configProvider);

        $this->permissionResolver = $permissionResolver;
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
    }

    protected function supports(View $view): bool
    {
        return $view instanceof ProductTypeView;
    }

    /**
     * @param \Ibexa\Bundle\ProductCatalog\View\ProductTypeView $view
     */
    protected function configureView(View $view): void
    {
        $productType = $view->getProductType();

        $view->setEditable(true);
        if ($this->permissionResolver->canUser(new Delete($productType))) {
            $view->addParameters([
                'delete_form' => $this->createDeleteForm($productType)->createView(),
            ]);
        }
    }

    private function createDeleteForm(ProductTypeInterface $productType): FormInterface
    {
        $actionUrl = $this->urlGenerator->generate(
            'ibexa.product_catalog.product_type.delete',
            [
                'productTypeIdentifier' => $productType->getIdentifier(),
            ]
        );

        return $this->formFactory->create(
            ProductTypeDeleteType::class,
            new ProductTypeDeleteData($productType),
            [
                'method' => Request::METHOD_POST,
                'action' => $actionUrl,
            ]
        );
    }
}
