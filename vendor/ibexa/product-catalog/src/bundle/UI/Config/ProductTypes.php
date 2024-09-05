<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI\Config;

use Ibexa\AdminUi\UI\Service\ContentTypeIconResolver;
use Ibexa\Contracts\AdminUi\UI\Config\ProviderInterface;
use Ibexa\Contracts\Core\Repository\Iterator\BatchIterator;
use Ibexa\Contracts\ProductCatalog\Iterator\BatchIteratorAdapter\ProductTypeListAdapter;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface as CatalogConfigProviderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ProductTypes implements ProviderInterface
{
    private ProviderInterface $innerProvider;

    private CatalogConfigProviderInterface $catalogConfigProvider;

    private LocalProductTypeServiceInterface $productTypeService;

    private ContentTypeIconResolver $contentTypeIconResolver;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        ProviderInterface $innerProvider,
        CatalogConfigProviderInterface $catalogConfigProvider,
        LocalProductTypeServiceInterface $productTypeService,
        ContentTypeIconResolver $contentTypeIconResolver,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->innerProvider = $innerProvider;
        $this->catalogConfigProvider = $catalogConfigProvider;
        $this->productTypeService = $productTypeService;
        $this->contentTypeIconResolver = $contentTypeIconResolver;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @phpstan-return array<string,array{
     *     id: int,
     *     identifier: string,
     *     name: string,
     *     isContainer: bool,
     *     thumbnail: string,
     *     href: string,
     *     isHidden: bool
     * }>
     */
    public function getConfig(): array
    {
        $config = $this->innerProvider->getConfig();
        if (!$this->isLocal()) {
            return $config;
        }

        $group = $this->getGroupIdentifier();

        $config[$group] = [];
        /** @var iterable<\Ibexa\Contracts\ProductCatalog\Values\ContentTypeAwareProductTypeInterface> $types */
        $types = new BatchIterator(new ProductTypeListAdapter($this->productTypeService));
        foreach ($types as $type) {
            $config[$group][] = [
                'id' => $type->getContentType()->id,
                'identifier' => $type->getIdentifier(),
                'name' => $type->getName(),
                'isContainer' => false,
                'thumbnail' => $this->getThumbnail($type),
                'href' => $this->getGenerateRestUrl($type),
                'isHidden' => true,
            ];
        }

        return $config;
    }

    /**
     * Returns true if current product catalog implementation is editable.
     */
    private function isLocal(): bool
    {
        if ($this->catalogConfigProvider->getEngineAlias() !== null) {
            return $this->catalogConfigProvider->getEngineType() === 'local';
        }

        return false;
    }

    private function getGroupIdentifier(): string
    {
        return $this->catalogConfigProvider->getEngineOption('product_type_group_identifier');
    }

    private function getGenerateRestUrl(ProductTypeInterface $type): string
    {
        return $this->urlGenerator->generate(
            'ibexa.rest.load_content_type',
            [
                'contentTypeId' => $type->getContentType()->id,
            ]
        );
    }

    private function getThumbnail(ProductTypeInterface $type): string
    {
        return $this->contentTypeIconResolver->getContentTypeIcon($type->getIdentifier());
    }
}
