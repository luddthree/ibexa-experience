<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\Currency;

use Ibexa\Bundle\ProductCatalog\Form\Data\Currency\CurrencyListFormData;
use Ibexa\Bundle\ProductCatalog\Form\Type\Currency\CurrencySearchType;
use Ibexa\Bundle\ProductCatalog\View\CurrencyListView;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Commerce\AdministrateCurrencies;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyQuery;
use Ibexa\Contracts\ProductCatalog\Values\Currency\Query\Criterion\CurrencyCodeCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Currency\Query\Criterion\IsCurrencyEnabledCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Currency\Query\SortClause\CurrencyCode;
use Ibexa\ProductCatalog\Pagerfanta\Adapter\CurrencyListAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

final class ListController extends AbstractController
{
    private CurrencyServiceInterface $currencyService;

    private ConfigResolverInterface $configResolver;

    public function __construct(CurrencyServiceInterface $currencyService, ConfigResolverInterface $configResolver)
    {
        $this->currencyService = $currencyService;
        $this->configResolver = $configResolver;
    }

    public function renderAction(Request $request): CurrencyListView
    {
        $this->denyAccessUnlessGranted(new AdministrateCurrencies());

        $searchForm = $this->createSearchForm();
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $data = $searchForm->getData();
            $query = $this->buildCurrencyQuery($data);
        }

        $currencies = new Pagerfanta(new CurrencyListAdapter($this->currencyService, $query ?? null));
        $currencies->setMaxPerPage($this->configResolver->getParameter('product_catalog.pagination.currencies_limit'));
        $currencies->setCurrentPage($request->query->getInt('page', 1));

        return new CurrencyListView('@ibexadesign/product_catalog/currency/index.html.twig', $currencies, $searchForm);
    }

    private function buildCurrencyQuery(CurrencyListFormData $data): CurrencyQuery
    {
        $criteria = null;
        if ($data->getQuery() !== null) {
            $criteria[] = new CurrencyCodeCriterion($data->getQuery());
        }

        $sort = [
            new CurrencyCode(),
        ];

        if ($data->getStatus() !== null) {
            $criteria[] = new IsCurrencyEnabledCriterion($data->getStatus());
        }

        if (is_array($criteria)) {
            $criteria = new LogicalAnd(...$criteria);
        }

        return new CurrencyQuery($criteria, $sort);
    }

    private function createSearchForm(): FormInterface
    {
        return $this->createForm(CurrencySearchType::class, null, [
            'method' => Request::METHOD_GET,
            'csrf_protection' => false,
        ]);
    }
}
