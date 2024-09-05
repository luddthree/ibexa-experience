<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Bundle\ProductCatalog\Form\Data\ProductDeleteData;
use Ibexa\Bundle\ProductCatalog\Form\Type\ProductDeleteType;
use Ibexa\Bundle\ProductCatalog\UI\Completeness\CompletenessFactoryInterface;
use Ibexa\Bundle\ProductCatalog\UI\Language\PreviewLanguageResolverInterface;
use Ibexa\Bundle\ProductCatalog\View\ProductView;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Product\Delete;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class LocalProductViewSubscriber extends AbstractLocalViewSubscriber
{
    private PermissionResolverInterface $permissionResolver;

    private FormFactoryInterface $formFactory;

    private UrlGeneratorInterface $urlGenerator;

    private CompletenessFactoryInterface $completenessFactory;

    private PreviewLanguageResolverInterface $languageResolver;

    public function __construct(
        SiteAccessServiceInterface $siteAccessService,
        ConfigProviderInterface $configProvider,
        PermissionResolverInterface $permissionResolver,
        FormFactoryInterface $formFactory,
        UrlGeneratorInterface $urlGenerator,
        CompletenessFactoryInterface $completenessFactory,
        PreviewLanguageResolverInterface $languageResolver
    ) {
        parent::__construct($siteAccessService, $configProvider);

        $this->permissionResolver = $permissionResolver;
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
        $this->completenessFactory = $completenessFactory;
        $this->languageResolver = $languageResolver;
    }

    protected function supports(View $view): bool
    {
        return $view instanceof ProductView;
    }

    /**
     * @param \Ibexa\Bundle\ProductCatalog\View\ProductView $view
     */
    protected function configureView(View $view): void
    {
        $product = $view->getProduct();
        $productDeleteForm = $this->createDeleteForm($product);

        $view->setEditable(true);

        if ($this->permissionResolver->canUser(new Delete())) {
            $view->addParameters([
                'delete_form' => $productDeleteForm->createView(),
            ]);
        }

        $view->addParameters([
            'completeness' => $this->completenessFactory->createProductCompleteness($product),
            'language' => $this->languageResolver->resolve(),
        ]);
    }

    private function createDeleteForm(ProductInterface $product): FormInterface
    {
        $actionUrl = $this->urlGenerator->generate(
            'ibexa.product_catalog.product.delete',
            [
                'productCode' => $product->getCode(),
            ]
        );

        return $this->formFactory->create(
            ProductDeleteType::class,
            new ProductDeleteData($product),
            [
                'method' => Request::METHOD_POST,
                'action' => $actionUrl,
            ]
        );
    }
}
