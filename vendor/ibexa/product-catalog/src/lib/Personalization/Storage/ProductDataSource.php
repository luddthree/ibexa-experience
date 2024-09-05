<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Personalization\Storage;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\Iterator\BatchIterator;
use Ibexa\Contracts\Core\Repository\Repository as RepositoryInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Personalization\Criteria\CriteriaInterface;
use Ibexa\Contracts\Personalization\Storage\DataSourceInterface;
use Ibexa\Contracts\Personalization\Value\ItemInterface;
use Ibexa\Contracts\ProductCatalog\Iterator\BatchIteratorAdapter\VariantFetchAdapter;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\LanguageSettings;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductType;
use Ibexa\Personalization\Exception\ItemNotFoundException;
use Ibexa\Personalization\Value\Storage\Item;
use Ibexa\Personalization\Value\Storage\ItemList;
use Ibexa\Personalization\Value\Storage\ItemType;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\ProductVariant;
use Ibexa\ProductCatalog\Personalization\Product\DataResolverInterface;
use Psr\Log\LoggerInterface;

final class ProductDataSource implements DataSourceInterface
{
    private const LOCAL_CATALOG_ERROR_MESSAGE = 'Only Local Product Catalog could be used as a data source';

    private DataResolverInterface $dataResolver;

    private LoggerInterface $logger;

    private LocalProductServiceInterface $productService;

    private ConfigProviderInterface $configProvider;

    private ContentService $contentService;

    private RepositoryInterface $repository;

    public function __construct(
        DataResolverInterface $dataResolver,
        LoggerInterface $logger,
        LocalProductServiceInterface $productService,
        ConfigProviderInterface $configProvider,
        ContentService $contentService,
        RepositoryInterface $repository
    ) {
        $this->dataResolver = $dataResolver;
        $this->logger = $logger;
        $this->productService = $productService;
        $this->configProvider = $configProvider;
        $this->contentService = $contentService;
        $this->repository = $repository;
    }

    public function countItems(CriteriaInterface $criteria): int
    {
        if (!$this->isLocalProductCatalog()) {
            $this->logger->error(self::LOCAL_CATALOG_ERROR_MESSAGE);

            return 0;
        }

        $productQuery = new ProductQuery(
            new ProductType($criteria->getItemTypeIdentifiers()),
            null,
            [],
            0,
            0
        );

        $languageSettings = $this->getLanguageSettings($criteria->getLanguages());

        return $this->repository->sudo(
            fn (): int => $this->productService->findProducts($productQuery, $languageSettings)->getTotalCount()
        );
    }

    /**
     * @return iterable<\Ibexa\Contracts\Personalization\Value\ItemInterface>
     */
    public function fetchItems(CriteriaInterface $criteria): iterable
    {
        if (!$this->isLocalProductCatalog()) {
            $this->logger->error(self::LOCAL_CATALOG_ERROR_MESSAGE);

            return new ItemList([]);
        }

        $productQuery = new ProductQuery(
            new ProductType($criteria->getItemTypeIdentifiers())
        );

        $productQuery->setOffset($criteria->getOffset());
        $productQuery->setLimit($criteria->getLimit());

        $languageSettings = $this->getLanguageSettings($criteria->getLanguages());

        return $this->repository->sudo(
            function () use ($productQuery, $languageSettings): ItemList {
                $products = $this->productService->findProducts($productQuery, $languageSettings)->getProducts();
                $items = [];

                /** @var \Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface $product */
                foreach ($products as $product) {
                    $content = $product->getContent();
                    $languageCodes = array_intersect(
                        $this->getLanguageCodesFromVersionInfo($content->getVersionInfo()),
                        $languageSettings->getLanguages()
                    );

                    foreach ($languageCodes as $languageCode) {
                        $items[] = $this->createItem($product, $content, $languageCode);

                        if (!$product->isBaseProduct()) {
                            continue;
                        }

                        $variants = new BatchIterator(
                            new VariantFetchAdapter($this->productService, $product),
                            20
                        );

                        /** @var \Ibexa\ProductCatalog\Local\Repository\Values\ProductVariant $variant */
                        foreach ($variants as $variant) {
                            $items[] = $this->createVariantItem($variant, $content, $languageCode);
                        }
                    }
                }

                return new ItemList($items);
            }
        );
    }

    public function fetchItem(string $id, string $language): ItemInterface
    {
        if (!$this->isLocalProductCatalog()) {
            $this->logger->error(self::LOCAL_CATALOG_ERROR_MESSAGE);

            throw new ItemNotFoundException($id, $language, 0);
        }

        try {
            return $this->repository->sudo(
                function () use ($id, $language): ItemInterface {
                    return $this->getItem($id, $language);
                }
            );
        } catch (UnauthorizedException | NotFoundException | InvalidArgumentException $exception) {
            throw new ItemNotFoundException($id, $language, 0, $exception);
        }
    }

    /**
     * @param array<string> $languageCodes
     */
    private function getLanguageSettings(array $languageCodes): LanguageSettings
    {
        return new LanguageSettings($languageCodes, false);
    }

    private function getItem(string $id, string $language): ItemInterface
    {
        try {
            $content = $this->contentService->loadContent((int)$id, [$language]);

            $isProduct = $this->productService->isProduct($content);
            if (!$isProduct) {
                throw new ItemNotFoundException($id, $language, 0);
            }

            $product = $this->productService->getProductFromContent($content);

            /** @var \Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface $product */
            return $this->createItem($product, $content, $language);
        } catch (UnauthorizedException | NotFoundException | InvalidArgumentException $exception) {
            // Do nothing
        }

        $productVariant = $this->productService->getProductVariant($id, new LanguageSettings([$language]));

        /** @var \Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface $baseProduct */
        $baseProduct = $productVariant->getBaseProduct();

        /** @var \Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface $productVariant */
        return $this->createProductVariantItem($productVariant, $baseProduct->getContent(), $language);
    }

    private function createItem(
        ContentAwareProductInterface $product,
        Content $content,
        string $languageCode
    ): ItemInterface {
        return new Item(
            (string)$content->getVersionInfo()->getContentInfo()->getId(),
            ItemType::fromContentType($content->getContentType()),
            $languageCode,
            $this->dataResolver->resolve($product, $languageCode)
        );
    }

    private function createProductVariantItem(
        ContentAwareProductInterface $product,
        Content $content,
        string $languageCode
    ): ItemInterface {
        return new Item(
            $product->getCode(),
            ItemType::fromContentType($content->getContentType()),
            $languageCode,
            $this->dataResolver->resolve($product, $languageCode)
        );
    }

    private function createVariantItem(
        ProductVariant $variant,
        Content $content,
        string $languageCode
    ): ItemInterface {
        return new Item(
            $variant->getCode(),
            ItemType::fromContentType($content->getContentType()),
            $languageCode,
            $this->dataResolver->resolve($variant, $languageCode)
        );
    }

    private function isLocalProductCatalog(): bool
    {
        return $this->configProvider->getEngineType() === 'local';
    }

    /**
     * @return array<string>
     */
    private function getLanguageCodesFromVersionInfo(VersionInfo $versionInfo): array
    {
        $languageCodes = [];
        foreach ($versionInfo->getLanguages() as $language) {
            $languageCodes[] = $language->getLanguageCode();
        }

        return $languageCodes;
    }

    public static function getDefaultPriority(): int
    {
        return 20;
    }
}
