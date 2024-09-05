<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\ProductType;

use Ibexa\Bundle\ProductCatalog\Form\Type\ProductTypeSearchType;
use Ibexa\Bundle\ProductCatalog\View\ProductTypeListView;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\ProductType\View;
use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductType\ProductTypeQuery;
use Ibexa\ProductCatalog\Pagerfanta\Adapter\ProductTypeListAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

final class ListController extends Controller
{
    private ProductTypeServiceInterface $productTypeService;

    private ConfigResolverInterface $configResolver;

    public function __construct(ProductTypeServiceInterface $productTypeService, ConfigResolverInterface $configResolver)
    {
        $this->productTypeService = $productTypeService;
        $this->configResolver = $configResolver;
    }

    public function renderAction(Request $request): ProductTypeListView
    {
        $this->denyAccessUnlessGranted(new View());

        $query = new ProductTypeQuery();

        $searchForm = $this->createSearchForm();
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $query->setNamePrefix($searchForm->getData()->getQuery());
        }

        $productTypes = new Pagerfanta(new ProductTypeListAdapter($this->productTypeService, $query));
        $productTypes->setMaxPerPage($this->configResolver->getParameter('product_catalog.pagination.product_types_limit'));
        $productTypes->setCurrentPage($request->query->getInt('page', 1));

        return new ProductTypeListView('@ibexadesign/product_catalog/product_type/list.html.twig', $productTypes, $searchForm);
    }

    private function createSearchForm(): FormInterface
    {
        return $this->createForm(ProductTypeSearchType::class, null, [
            'action' => $this->generateUrl('ibexa.product_catalog.product_type.list'),
            'csrf_protection' => false,
            'method' => Request::METHOD_GET,
        ]);
    }
}
