<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\REST;

use Ibexa\Bundle\ProductCatalog\REST\Value\ProductView;
use Ibexa\Contracts\ProductCatalog\CatalogServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalAnd;
use Ibexa\Rest\Message;
use Ibexa\Rest\Server\Controller as RestController;
use Ibexa\Rest\Value;
use Symfony\Component\HttpFoundation\Request;

final class CatalogProductsViewController extends RestController
{
    private CatalogServiceInterface $catalogService;

    private ProductServiceInterface $productService;

    public function __construct(
        CatalogServiceInterface $catalogService,
        ProductServiceInterface $productService
    ) {
        $this->catalogService = $catalogService;
        $this->productService = $productService;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\ProductCatalog\Exception\UnauthorizedException
     */
    public function createView(Request $request, string $catalogIdentifier): Value
    {
        $viewInput = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        $catalog = $this->catalogService->getCatalogByIdentifier($catalogIdentifier);

        $query = new ProductQuery();

        $catalogCriterion = $catalog->getQuery();
        $inputCriteria = $viewInput->query->getQuery();

        if ($inputCriteria !== null) {
            $catalogCriterion = new LogicalAnd([$inputCriteria, $catalogCriterion]);
        }

        $query->setFilter($catalogCriterion);

        return new ProductView(
            $viewInput->identifier,
            $this->productService->findProducts($query)
        );
    }
}
