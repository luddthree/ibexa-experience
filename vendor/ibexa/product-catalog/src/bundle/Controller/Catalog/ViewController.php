<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\Catalog;

use Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\CatalogCopyData;
use Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\CatalogDeleteData;
use Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\CatalogTransitionData;
use Ibexa\Bundle\ProductCatalog\Form\Type\Catalog\CatalogCopyType;
use Ibexa\Bundle\ProductCatalog\Form\Type\Catalog\CatalogDeleteType;
use Ibexa\Bundle\ProductCatalog\Form\Type\Catalog\CatalogTransitionType;
use Ibexa\Bundle\ProductCatalog\View\CatalogDetailedView;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Catalog\View;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

final class ViewController extends AbstractController
{
    public function renderAction(CatalogInterface $catalog): CatalogDetailedView
    {
        $this->denyAccessUnlessGranted(new View());

        $view = new CatalogDetailedView('@ibexadesign/product_catalog/catalog/view.html.twig', $catalog);
        $view->addParameters([
            'delete_form' => $this->createDeleteForm($catalog)->createView(),
            'copy_form' => $this->createCopyForm($catalog)->createView(),
            'transition_form' => $this->createTransitionForm($catalog)->createView(),
        ]);

        return $view;
    }

    private function createDeleteForm(CatalogInterface $catalog): FormInterface
    {
        $actionUrl = $this->generateUrl(
            'ibexa.product_catalog.catalog.delete',
            [
                'catalogId' => $catalog->getId(),
            ]
        );

        return $this->createForm(
            CatalogDeleteType::class,
            new CatalogDeleteData($catalog),
            [
                'method' => Request::METHOD_POST,
                'action' => $actionUrl,
            ]
        );
    }

    private function createCopyForm(CatalogInterface $catalog): FormInterface
    {
        $actionUrl = $this->generateUrl(
            'ibexa.product_catalog.catalog.copy',
        );

        return $this->createForm(
            CatalogCopyType::class,
            new CatalogCopyData($catalog),
            [
                'method' => Request::METHOD_POST,
                'action' => $actionUrl,
            ]
        );
    }

    private function createTransitionForm(CatalogInterface $catalog): FormInterface
    {
        $actionUrl = $this->generateUrl(
            'ibexa.product_catalog.catalog.transition',
            [
                'catalogId' => $catalog->getId(),
            ]
        );

        return $this->createForm(
            CatalogTransitionType::class,
            new CatalogTransitionData(
                $catalog->getId(),
            ),
            [
                'method' => Request::METHOD_POST,
                'action' => $actionUrl,
                'catalog' => $catalog,
            ]
        );
    }
}
