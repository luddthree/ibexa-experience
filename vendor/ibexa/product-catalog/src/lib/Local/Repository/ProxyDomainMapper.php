<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Ibexa\Core\Repository\ProxyFactory\ProxyGeneratorInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\Product;
use Ibexa\ProductCatalog\Local\Repository\Values\ProductType;
use ProxyManager\Proxy\LazyLoadingInterface;

/**
 * @internal
 */
final class ProxyDomainMapper
{
    private Repository $repository;

    private ProxyGeneratorInterface $proxyGenerator;

    private ProductTypeServiceInterface $productTypeService;

    private ProductServiceInterface $productService;

    public function __construct(
        Repository $repository,
        ProductServiceInterface $productService,
        ProductTypeServiceInterface $productTypeService,
        ProxyGeneratorInterface $proxyGenerator
    ) {
        $this->repository = $repository;
        $this->productService = $productService;
        $this->productTypeService = $productTypeService;
        $this->proxyGenerator = $proxyGenerator;
    }

    /**
     * @param array<int, string> $prioritizedLanguages
     */
    public function createContentProxy(
        int $contentId,
        array $prioritizedLanguages = Language::ALL,
        bool $useAlwaysAvailable = true
    ): Content {
        $initializer = function (
            &$wrappedObject,
            LazyLoadingInterface $proxy,
            $method,
            array $parameters,
            &$initializer
        ) use ($contentId, $prioritizedLanguages, $useAlwaysAvailable): bool {
            $initializer = null;
            $wrappedObject = $this->repository->sudo(
                fn () => $this->repository->getContentService()->loadContent(
                    $contentId,
                    $prioritizedLanguages,
                    null,
                    $useAlwaysAvailable
                )
            );

            return true;
        };

        return $this->proxyGenerator->createProxy(Content::class, $initializer);
    }

    public function createProductProxy(
        string $code
    ): Product {
        $initializer = function (
            &$wrappedObject,
            LazyLoadingInterface $proxy,
            $method,
            array $parameters,
            &$initializer
        ) use ($code): bool {
            $initializer = null;
            $wrappedObject = $this->productService->getProduct($code);

            return true;
        };

        return $this->proxyGenerator->createProxy(Product::class, $initializer);
    }

    public function createProductTypeProxy(ContentType $contentType): ProductType
    {
        $initializer = function (
            &$wrappedObject,
            LazyLoadingInterface $proxy,
            $method,
            array $parameters,
            &$initializer
        ) use ($contentType): bool {
            $initializer = null;
            $wrappedObject = $this->productTypeService->getProductType($contentType->identifier);

            return true;
        };

        return $this->proxyGenerator->createProxy(ProductType::class, $initializer);
    }

    public function createProductTypeProxyFromContent(Content $content): ProductType
    {
        $initializer = function (
            &$wrappedObject,
            LazyLoadingInterface $proxy,
            $method,
            array $parameters,
            &$initializer
        ) use ($content): bool {
            $initializer = null;
            $wrappedObject = $this->productTypeService->getProductType(
                $content->getContentType()->identifier
            );

            return true;
        };

        return $this->proxyGenerator->createProxy(ProductType::class, $initializer);
    }

    /**
     * @param array<int, string> $prioritizedLanguages
     */
    public function createUserProxy(
        int $creatorId,
        array $prioritizedLanguages = Language::ALL
    ): User {
        $initializer = function (
            &$wrappedObject,
            LazyLoadingInterface $proxy,
            $method,
            array $parameters,
            &$initializer
        ) use ($creatorId, $prioritizedLanguages): bool {
            $initializer = null;
            $wrappedObject = $this->repository->sudo(
                fn () => $this->repository->getUserService()->loadUser(
                    $creatorId,
                    $prioritizedLanguages
                )
            );

            return true;
        };

        return $this->proxyGenerator->createProxy(User::class, $initializer);
    }
}
