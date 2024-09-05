<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\Regions;

use Ibexa\Bundle\Core\Controller;
use Ibexa\Bundle\ProductCatalog\Form\Data\SearchQueryData;
use Ibexa\Bundle\ProductCatalog\Form\Type\SearchType;
use Ibexa\Bundle\ProductCatalog\View\RegionListView;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Commerce\AdministrateRegions;
use Ibexa\Contracts\ProductCatalog\RegionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Region\Query\Criterion\RegionIdentifierCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Region\RegionQuery;
use Ibexa\Contracts\ProductCatalog\VatServiceInterface;
use Ibexa\ProductCatalog\Pagerfanta\Adapter\RegionListAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

final class ListController extends Controller
{
    private RegionServiceInterface $regionService;

    private VatServiceInterface $vatService;

    private ConfigResolverInterface $configResolver;

    public function __construct(
        RegionServiceInterface $regionService,
        VatServiceInterface $vatService,
        ConfigResolverInterface $configResolver
    ) {
        $this->regionService = $regionService;
        $this->vatService = $vatService;
        $this->configResolver = $configResolver;
    }

    public function renderAction(Request $request): RegionListView
    {
        $this->denyAccessUnlessGranted(new AdministrateRegions());

        $searchForm = $this->createSearchForm();
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $data = $searchForm->getData();
            $query = $this->buildQuery($data);
        }

        $regions = new Pagerfanta(new RegionListAdapter($this->regionService, $query ?? null));
        $regions->setMaxPerPage($this->configResolver->getParameter('product_catalog.pagination.regions_limit'));
        $regions->setCurrentPage($request->query->getInt('page', 1));

        $vatCategoriesByRegion = [];
        /** @var \Ibexa\Contracts\ProductCatalog\Values\RegionInterface $region */
        foreach ($regions as $region) {
            $vatCategoriesList = $this->vatService->getVatCategories($region);
            $vatCategories = $vatCategoriesList->getVatCategories();
            $vatCategoriesByRegion[$region->getIdentifier()] = $vatCategories;
        }

        return new RegionListView('@ibexadesign/product_catalog/region/list.html.twig', [
            'regions' => $regions,
            'vat_categories_by_region' => $vatCategoriesByRegion,
            'search_form' => $searchForm->createView(),
        ]);
    }

    private function buildQuery(SearchQueryData $data): RegionQuery
    {
        $criteria = null;
        if ($data->getQuery() !== null) {
            $criteria = new RegionIdentifierCriterion($data->getQuery(), FieldValueCriterion::COMPARISON_STARTS_WITH);
        }

        return new RegionQuery($criteria);
    }

    private function createSearchForm(): FormInterface
    {
        return $this->createForm(SearchType::class);
    }
}
