<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\EventSubscriber\PageBuilder;

use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\TermAggregationResult;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalNot;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductCategorySubtree;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\BlockRenderEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent;
use Ibexa\ProductCatalog\QueryType\Product\Block\ProductsByCategoriesQueryType;
use Ibexa\Taxonomy\Exception\TaxonomyEntryNotFoundException;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ProductsByCategoriesBlockSubscriber implements EventSubscriberInterface
{
    private const BLOCK_IDENTIFIER = 'products_by_categories';

    private ProductServiceInterface $productService;

    private ProductsByCategoriesQueryType $queryType;

    private TaxonomyServiceInterface $taxonomyService;

    private TranslatorInterface $translator;

    private string $productTaxonomyName;

    public function __construct(
        ProductServiceInterface $productService,
        ProductsByCategoriesQueryType $queryType,
        TaxonomyServiceInterface $taxonomyService,
        TranslatorInterface $translator,
        string $productTaxonomyName
    ) {
        $this->queryType = $queryType;
        $this->productService = $productService;
        $this->taxonomyService = $taxonomyService;
        $this->productTaxonomyName = $productTaxonomyName;
        $this->translator = $translator;
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

        /** @var iterable<\Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry, int>|null $distribution */
        $distribution = $this->getCategoriesDistribution($request->getParameters());
        if ($distribution === null) {
            return;
        }

        $chartData = [];
        $distributionCount = 0;
        foreach ($distribution as $category => $count) {
            $chartData[] = [
                'label' => $category->getName(),
                'value' => $count,
            ];
            $distributionCount += $count;
        }
        $this->addUncategorizedProductsCount($chartData);
        $this->addAllCategorizedProductsCount($chartData, $distributionCount);

        $request->addParameter(
            'chart_data',
            $chartData
        );
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function getCategoriesDistribution(array $parameters): ?TermAggregationResult
    {
        $query = $this->queryType->getQuery([
            'limit' => (int) $parameters['limit'],
        ]);

        $results = $this->productService->findProducts($query);
        if ($results->getAggregations() === null || $results->getAggregations()->isEmpty()) {
            return null;
        }

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\TermAggregationResult */
        return $results->getAggregations()->get(ProductsByCategoriesQueryType::DEFAULT_AGGREGATION_NAME);
    }

    /**
     * @param array{'label': string, 'value': int} $chartData
     */
    private function addUncategorizedProductsCount(array &$chartData): void
    {
        $label = $this->translator->trans(
            /** @Desc("Uncategorized") */
            'dashboard.products_by_categories.uncategorized',
            [],
            'ibexa_dashboard'
        );

        try {
            $categoryRoot = $this->taxonomyService->loadRootEntry($this->productTaxonomyName);
        } catch (TaxonomyEntryNotFoundException $e) {
            $chartData[] = [
                'label' => $label,
                'value' => 0,
            ];

            return;
        }

        $productQuery = new ProductQuery();
        $productQuery->setLimit(0);
        $productQuery->setFilter(
            new LogicalNot(
                new ProductCategorySubtree($categoryRoot->getId())
            )
        );

        $chartData[] = [
            'label' => $label,
            'value' => $this->productService->findProducts($productQuery)->getTotalCount(),
        ];
    }

    /**
     * @param array{'label': string, 'value': int} $chartData
     */
    private function addAllCategorizedProductsCount(array &$chartData, int $distributionCount): void
    {
        $label = $this->translator->trans(
            /** @Desc("Other categories") */
            'dashboard.products_by_categories.other_categories',
            [],
            'ibexa_dashboard'
        );

        try {
            $categoryRoot = $this->taxonomyService->loadRootEntry($this->productTaxonomyName);
        } catch (TaxonomyEntryNotFoundException $e) {
            $chartData[] = [
                'label' => $label,
                'value' => 0,
            ];

            return;
        }

        $productQuery = new ProductQuery();
        $productQuery->setLimit(0);
        $productQuery->setFilter(new ProductCategorySubtree($categoryRoot->getId()));

        $chartData[] = [
            'label' => $label,
            'value' => $this->productService->findProducts($productQuery)->getTotalCount() - $distributionCount,
        ];
    }
}
