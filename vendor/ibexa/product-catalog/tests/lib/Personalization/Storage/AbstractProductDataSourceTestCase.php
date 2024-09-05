<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Personalization\Storage;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Personalization\Storage\DataSourceInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;
use Ibexa\ProductCatalog\Personalization\Product\DataResolverInterface;
use Ibexa\ProductCatalog\Personalization\Storage\ProductDataSource;
use Ibexa\Tests\ProductCatalog\Personalization\Creator\TestProductFactory;
use Psr\Log\LoggerInterface;

abstract class AbstractProductDataSourceTestCase extends AbstractDataSourceTestCase
{
    use TestProductFactory;

    protected DataSourceInterface $productDataSource;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService|\PHPUnit\Framework\MockObject\MockObject */
    protected ContentService $contentService;

    /** @var \Ibexa\ProductCatalog\Personalization\Product\DataResolverInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected DataResolverInterface $dataResolver;

    /** @var \Psr\Log\LoggerInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected LoggerInterface $logger;

    /** @var \Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected LocalProductServiceInterface $productService;

    /** @var \Ibexa\ProductCatalog\Config\ConfigProviderInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected ConfigProviderInterface $configProvider;

    /** @var \Ibexa\Contracts\Core\Repository\Repository|\PHPUnit\Framework\MockObject\MockObject */
    protected Repository $repository;

    protected function setUp(): void
    {
        $this->contentService = $this->createMock(ContentService::class);
        $this->dataResolver = $this->createMock(DataResolverInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->dataResolver = $this->createMock(DataResolverInterface::class);
        $this->productService = $this->createMock(LocalProductServiceInterface::class);
        $this->configProvider = $this->createMock(ConfigProviderInterface::class);
        $this->configProvider->method('getEngineType')->willReturn('local');

        $this->repository = $this->createRepositoryMock();

        $this->productDataSource = new ProductDataSource(
            $this->dataResolver,
            $this->logger,
            $this->productService,
            $this->configProvider,
            $this->contentService,
            $this->repository
        );
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Repository|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function createRepositoryMock(): Repository
    {
        $callback = fn (callable $callable) => $callable($this->repository);

        $repository = $this->createMock(Repository::class);
        $repository->method('sudo')->willReturnCallback($callback);

        return $repository;
    }
}
