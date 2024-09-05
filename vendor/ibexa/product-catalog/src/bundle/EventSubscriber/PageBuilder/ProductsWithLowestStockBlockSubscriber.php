<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\EventSubscriber\PageBuilder;

use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\QueryTypeRegistryInterface;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\BlockRenderEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent;
use Ibexa\ProductCatalog\QueryType\Product\ProductsWithLowestStockQueryType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ProductsWithLowestStockBlockSubscriber implements EventSubscriberInterface
{
    private const BLOCK_IDENTIFIER = 'products_with_lowest_stock';

    private ProductServiceInterface $productService;

    private QueryTypeRegistryInterface $queryTypeRegistry;

    public function __construct(
        ProductServiceInterface $productService,
        QueryTypeRegistryInterface $queryTypeRegistry
    ) {
        $this->productService = $productService;
        $this->queryTypeRegistry = $queryTypeRegistry;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BlockRenderEvents::getBlockPreRenderEventName(self::BLOCK_IDENTIFIER) => 'onBlockPreRender',
        ];
    }

    public function onBlockPreRender(PreRenderEvent $event): void
    {
        /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Twig\TwigRenderRequest $request */
        $request = $event->getRenderRequest();
        $request->addParameter('block_name', $event->getBlockValue()->getName());

        $queryType = $this->queryTypeRegistry->getQueryType(ProductsWithLowestStockQueryType::NAME);

        $productList = $this->productService->findProducts($queryType->getQuery($request->getParameters()));

        $request->addParameter(
            'products',
            $productList
        );
    }
}
