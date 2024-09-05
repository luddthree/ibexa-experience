<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\REST;

use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion\ProductCriteriaRegistry;
use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion\ProductSortClausesRegistry;
use Ibexa\Bundle\ProductCatalog\REST\Value\Catalog;
use Ibexa\Bundle\ProductCatalog\REST\Value\CatalogProductFilter;
use Ibexa\Bundle\ProductCatalog\REST\Value\CatalogProductFilterList;
use Ibexa\Bundle\ProductCatalog\REST\Value\CatalogProductSortClause;
use Ibexa\Bundle\ProductCatalog\REST\Value\CatalogProductSortClauseList;
use Ibexa\Contracts\ProductCatalog\CatalogServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogCopyStruct;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogUpdateStruct;
use Ibexa\Rest\Message;
use Ibexa\Rest\Server\Controller as RestController;
use Ibexa\Rest\Server\Values\NoContent;
use Ibexa\Rest\Value;
use Symfony\Component\HttpFoundation\Request;

final class CatalogController extends RestController
{
    private CatalogServiceInterface $catalogService;

    private ProductCriteriaRegistry $productCriteriaRegistry;

    private ProductSortClausesRegistry $productSortClausesRegistry;

    public function __construct(
        CatalogServiceInterface $catalogService,
        ProductCriteriaRegistry $productCriteriaRegistry,
        ProductSortClausesRegistry $productSortClausesRegistry
    ) {
        $this->catalogService = $catalogService;
        $this->productCriteriaRegistry = $productCriteriaRegistry;
        $this->productSortClausesRegistry = $productSortClausesRegistry;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\ProductCatalog\Exception\UnauthorizedException
     */
    public function getCatalogAction(string $identifier): Value
    {
        $catalog = $this->catalogService->getCatalogByIdentifier($identifier);

        return new Catalog($catalog);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function createCatalogAction(Request $request): Value
    {
        /** @var \Ibexa\Bundle\ProductCatalog\REST\Value\CatalogCreateStruct $input */
        $input = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        $catalogCreateStruct = new CatalogCreateStruct(
            $input->getIdentifier(),
            $input->getCriterion(),
            $input->getNames(),
            $input->getDescriptions(),
            $input->getStatus(),
            $input->getCreatorId()
        );

        $catalog = $this->catalogService->createCatalog($catalogCreateStruct);

        return new Catalog($catalog);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\ProductCatalog\Exception\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    public function updateCatalogAction(Request $request, string $identifier): Value
    {
        /** @var \Ibexa\Bundle\ProductCatalog\REST\Value\CatalogUpdateStruct $input */
        $input = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        $catalog = $this->catalogService->getCatalogByIdentifier($identifier);

        $catalogUpdateStruct = new CatalogUpdateStruct(
            $catalog->getId(),
            $input->getTransition(),
            $input->getIdentifier(),
            $input->getCriterion(),
            $input->getNames(),
            $input->getDescriptions()
        );

        $updated = $this->catalogService->updateCatalog($catalog, $catalogUpdateStruct);

        return new Catalog($updated);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function deleteCatalogAction(string $identifier): Value
    {
        $catalog = $this->catalogService->getCatalogByIdentifier($identifier);
        $this->catalogService->deleteCatalog($catalog);

        return new NoContent();
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function copyCatalogAction(Request $request, string $identifier): Value
    {
        $catalog = $this->catalogService->getCatalogByIdentifier($identifier);

        /** @var \Ibexa\Bundle\ProductCatalog\REST\Value\CatalogCopyStruct $input */
        $input = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        $catalogCopyStruct = new CatalogCopyStruct(
            $catalog->getId(),
            $input->getIdentifier(),
            $input->getCreatorId()
        );

        $copy = $this->catalogService->copyCatalog($catalog, $catalogCopyStruct);

        return new Catalog($copy);
    }

    public function getFiltersAction(): CatalogProductFilterList
    {
        $restFilters = [];
        $productCriteria = $this->productCriteriaRegistry->getCriteria();

        foreach ($productCriteria as $criterion) {
            $restFilters[] = new CatalogProductFilter($criterion->getName());
        }

        return new CatalogProductFilterList($restFilters);
    }

    public function getSortClausesAction(): CatalogProductSortClauseList
    {
        $restSortClauses = [];
        $productSortClauses = $this->productSortClausesRegistry->getSortClauses();

        foreach ($productSortClauses as $sortClause) {
            $restSortClauses[] = new CatalogProductSortClause($sortClause->getDataKey());
        }

        return new CatalogProductSortClauseList($restSortClauses);
    }
}
