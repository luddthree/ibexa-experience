<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\Catalog;

use Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\CatalogListFormData;
use Ibexa\Bundle\ProductCatalog\Form\Type\Catalog\CatalogSearchType;
use Ibexa\Bundle\ProductCatalog\View\CatalogListView;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\ProductCatalog\CatalogServiceInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Catalog\View;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogQuery;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\Query\Criterion\CatalogIdentifier;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\Query\Criterion\CatalogName;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\Query\Criterion\CatalogStatus;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion as BaseFieldValueCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\LogicalOr;
use Ibexa\ProductCatalog\Pagerfanta\Adapter\CatalogListAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

final class ListController extends AbstractController
{
    private CatalogServiceInterface $catalogService;

    private ConfigResolverInterface $configResolver;

    public function __construct(
        CatalogServiceInterface $catalogService,
        ConfigResolverInterface $configResolver
    ) {
        $this->catalogService = $catalogService;
        $this->configResolver = $configResolver;
    }

    public function renderAction(Request $request): CatalogListView
    {
        $this->denyAccessUnlessGranted(new View());

        $searchForm = $this->createSearchForm();
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $data = $searchForm->getData();
            $query = $this->buildCatalogQuery($data);
        }

        $catalogs = new Pagerfanta(new CatalogListAdapter($this->catalogService, $query ?? null));
        $catalogs->setMaxPerPage($this->configResolver->getParameter('product_catalog.pagination.catalogs_limit'));
        $catalogs->setCurrentPage($request->query->getInt('page', 1));

        return new CatalogListView('@ibexadesign/product_catalog/catalog/list.html.twig', $catalogs, $searchForm);
    }

    private function buildCatalogQuery(CatalogListFormData $data): CatalogQuery
    {
        $criteria = null;
        if ($data->getQuery() !== null) {
            $criteria[] = new LogicalOr(
                new CatalogName($data->getQuery(), BaseFieldValueCriterion::COMPARISON_STARTS_WITH),
                new CatalogIdentifier($data->getQuery()),
            );
        }

        if ($data->getStatus() !== null) {
            $criteria[] = new CatalogStatus($data->getStatus());
        }

        if (is_array($criteria)) {
            $criteria = new LogicalAnd(...$criteria);
        }

        return new CatalogQuery($criteria);
    }

    private function createSearchForm(): FormInterface
    {
        return $this->createForm(CatalogSearchType::class, null, [
            'method' => Request::METHOD_GET,
            'csrf_protection' => false,
        ]);
    }
}
