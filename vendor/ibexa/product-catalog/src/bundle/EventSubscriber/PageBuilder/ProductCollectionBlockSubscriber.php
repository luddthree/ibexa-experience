<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\EventSubscriber\PageBuilder;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductListInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductCode;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\BlockRenderEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class ProductCollectionBlockSubscriber implements EventSubscriberInterface
{
    private const BLOCK_IDENTIFIER = 'product_collection';
    private const PAGE_PARAM = 'page_';
    private const DEFAULT_CURRENT_PAGE = 1;

    private ProductServiceInterface $productService;

    private RequestStack $requestStack;

    public function __construct(
        ProductServiceInterface $productService,
        RequestStack $requestStack
    ) {
        $this->productService = $productService;
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BlockRenderEvents::getBlockPreRenderEventName(self::BLOCK_IDENTIFIER) => 'onBlockPreRender',
        ];
    }

    public function onBlockPreRender(PreRenderEvent $event): void
    {
        /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Twig\TwigRenderRequest $renderRequest */
        $renderRequest = $event->getRenderRequest();
        $parameters = $renderRequest->getParameters();
        $blockValue = $event->getBlockValue();
        $codes = $this->getProductsCodes($blockValue);
        $query = $this->getProductQuery($codes);
        $products = $this->sortByProductCodes(
            $this->getProductList($query),
            array_flip($codes)
        );

        $parameters['products'] = $this->getPagerfanta(
            $products,
            $query->getLimit(),
            $this->getCurrentPage(self::PAGE_PARAM . $blockValue->getId())
        );

        $renderRequest->setParameters($parameters);
    }

    /**
     * @param array<string> $codes
     */
    private function getProductQuery(array $codes): ProductQuery
    {
        return new ProductQuery(
            new LogicalAnd(
                [
                    new ProductCode($codes),
                ]
            )
        );
    }

    /**
     * @return array<string>
     */
    private function getProductsCodes(BlockValue $blockValue): array
    {
        $blockAttribute = $blockValue->getAttribute('products');

        if (null === $blockAttribute) {
            return [];
        }

        return explode(',', $blockAttribute->getValue());
    }

    private function getProductList(ProductQuery $query): ProductListInterface
    {
        return $this->productService->findProducts($query);
    }

    /**
     * @param array<\Ibexa\Contracts\ProductCatalog\Values\ProductInterface> $products
     *
     * @return \Pagerfanta\Pagerfanta<\Ibexa\Contracts\ProductCatalog\Values\ProductInterface>
     */
    private function getPagerfanta(
        array $products,
        int $limit,
        int $currentPage
    ): Pagerfanta {
        $pagerfanta = new Pagerfanta(new ArrayAdapter($products));
        $pagerfanta->setMaxPerPage($limit);
        $pagerfanta->setCurrentPage($currentPage);

        return $pagerfanta;
    }

    /**
     * @param array<string, int> $codes
     *
     * @return array<\Ibexa\Contracts\ProductCatalog\Values\ProductInterface>
     */
    private function sortByProductCodes(
        ProductListInterface $products,
        array $codes
    ): array {
        $results = [];

        foreach ($products as $product) {
            if (isset($codes[$product->getCode()])) {
                $results[$codes[$product->getCode()]] = $product;
            }
        }

        ksort($results);

        return $results;
    }

    private function getCurrentPage(string $pageParam): int
    {
        $request = $this->requestStack->getMainRequest();

        if (null === $request) {
            return self::DEFAULT_CURRENT_PAGE;
        }

        return $request->query->getInt($pageParam, self::DEFAULT_CURRENT_PAGE);
    }
}
