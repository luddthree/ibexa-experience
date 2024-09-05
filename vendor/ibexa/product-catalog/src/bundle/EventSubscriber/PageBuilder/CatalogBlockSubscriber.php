<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\EventSubscriber\PageBuilder;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\Contracts\ProductCatalog\CatalogServiceInterface;
use Ibexa\Contracts\ProductCatalog\CustomerGroupResolverInterface;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\BlockRenderEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent;
use Ibexa\ProductCatalog\Pagerfanta\Adapter\ProductListAdapter;
use Pagerfanta\Pagerfanta;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class CatalogBlockSubscriber implements EventSubscriberInterface, LoggerAwareInterface
{
    private const BLOCK_IDENTIFIER = 'catalog';
    private const PAGE_PARAM = 'page_';
    private const DEFAULT_CURRENT_PAGE = 1;

    use LoggerAwareTrait;

    private CatalogServiceInterface $catalogService;

    private CustomerGroupResolverInterface $customerGroupResolver;

    private ProductServiceInterface $productService;

    private RequestStack $requestStack;

    public function __construct(
        CatalogServiceInterface $catalogService,
        CustomerGroupResolverInterface $customerGroupResolver,
        ProductServiceInterface $productService,
        RequestStack $requestStack,
        ?LoggerInterface $logger = null
    ) {
        $this->catalogService = $catalogService;
        $this->customerGroupResolver = $customerGroupResolver;
        $this->productService = $productService;
        $this->requestStack = $requestStack;
        $this->logger = $logger ?? new NullLogger();
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
        $catalog = $this->getMappedCatalog($blockValue) ?? $this->getDefaultCatalog($blockValue);
        $products = [];

        if (null !== $catalog) {
            $products = $this->getProductList(
                $catalog->getQuery(),
                (int) $parameters['limit'],
                $this->getCurrentPage(self::PAGE_PARAM . $blockValue->getId())
            );
        }

        $parameters['products'] = $products;

        unset($parameters['default_catalog'], $parameters['catalog_map']);

        $renderRequest->setParameters($parameters);
    }

    private function getMappedCatalog(BlockValue $blockValue): ?CatalogInterface
    {
        $catalogMap = $this->getCatalogMap($blockValue);
        $customerGroup = $this->resolveCustomerGroup();

        if (
            !empty($catalogMap)
            && null !== $customerGroup
        ) {
            foreach ($catalogMap as $data) {
                if ($customerGroup->getId() === $data['customer_group']) {
                    return $this->getCatalog((int) $data['catalog']);
                }
            }
        }

        return null;
    }

    /**
     * @return \Pagerfanta\Pagerfanta<\Ibexa\Contracts\ProductCatalog\Values\ProductInterface>
     */
    private function getProductList(
        CriterionInterface $criterion,
        int $limit,
        int $currentPage
    ): Pagerfanta {
        $products = new Pagerfanta(
            new ProductListAdapter(
                $this->productService,
                new ProductQuery($criterion)
            )
        );
        $products->setMaxPerPage($limit);
        $products->setCurrentPage($currentPage);

        return $products;
    }

    private function resolveCustomerGroup(): ?CustomerGroupInterface
    {
        try {
            return $this->customerGroupResolver->resolveCustomerGroup();
        } catch (NotFoundException | UnauthorizedException $exception) {
            $this->logger->warning($exception->getMessage());

            return null;
        }
    }

    private function getCatalog(int $id): ?CatalogInterface
    {
        try {
            return $this->catalogService->getCatalog($id);
        } catch (NotFoundException | UnauthorizedException $exception) {
            $this->logger->warning($exception->getMessage());

            return null;
        }
    }

    private function getDefaultCatalog(BlockValue $blockValue): ?CatalogInterface
    {
        $catalog = $blockValue->getAttribute('default_catalog');

        if (null === $catalog) {
            return null;
        }

        return $this->getCatalog((int) $catalog->getValue());
    }

    /**
     * @return array<array{
     *     'customer_group': int,
     *     'catalog': int,
     * }>
     *
     * @throws \JsonException
     */
    private function getCatalogMap(BlockValue $blockValue): array
    {
        $catalogMapAttribute = $blockValue->getAttribute('catalog_map');
        if (null === $catalogMapAttribute) {
            return [];
        }

        return json_decode(
            $catalogMapAttribute->getValue(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
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
