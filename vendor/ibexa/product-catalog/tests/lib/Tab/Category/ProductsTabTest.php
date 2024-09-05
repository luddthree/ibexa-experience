<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Tab\Category;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\QueryTypeRegistryInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductListInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\ProductCatalog\QueryType\Product\SearchQueryType;
use Ibexa\ProductCatalog\Tab\Category\ProductsTab;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class ProductsTabTest extends TestCase
{
    private const TEST_CONTENT_ID = 12345;

    private ProductsTab $productsTab;

    /** @var \Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface&\PHPUnit\Framework\MockObject\MockObject */
    private TaxonomyServiceInterface $taxonomyServiceMock;

    protected function setUp(): void
    {
        $this->taxonomyServiceMock = $this->createTaxonomyServiceMock();

        $this->productsTab = new ProductsTab(
            $this->createMock(Environment::class),
            $this->createMock(TranslatorInterface::class),
            $this->createMock(EventDispatcherInterface::class),
            $this->createProductServiceMock(),
            $this->createConfigResolverMock(),
            $this->createMock(RequestStack::class),
            $this->createTaxonomyConfigurationMock(),
            $this->taxonomyServiceMock,
            $this->createFormFactoryMock(),
            $this->createQueryTypeRegistryMock()
        );
    }

    /**
     * @dataProvider providerForEvaluate
     *
     * @param mixed $evaluatedParameter
     */
    public function testEvaluate($evaluatedParameter, bool $expectedResult): void
    {
        $this->taxonomyServiceMock->method('loadEntryByContentId')->willReturn(
            $this->getTaxonomyEntry()
        );

        self::assertSame(
            $expectedResult,
            $this->productsTab->evaluate([
                $this->getContentTypeParameterKey() => $evaluatedParameter,
                $this->getContentParameterKey() => $this->getContent(),
            ])
        );
    }

    /**
     * @return iterable<string,array{mixed,bool}>
     */
    public function providerForEvaluate(): iterable
    {
        yield 'Content type with taxonomy' => [
            $this->getContentTypeWithTaxonomy(),
            true,
        ];

        yield 'Content type without taxonomy' => [
            $this->getContentTypeWithoutTaxonomy(),
            false,
        ];
    }

    /**
     * @dataProvider providerForEvaluateWithContent
     *
     * @param mixed $evaluatedParameter
     */
    public function testEvaluateWithContent($evaluatedParameter, bool $withParent, bool $expectedResult): void
    {
        $this->taxonomyServiceMock->method('loadEntryByContentId')->willReturn(
            $this->getTaxonomyEntry($withParent)
        );

        self::assertSame(
            $expectedResult,
            $this->productsTab->evaluate([
                $this->getContentTypeParameterKey() => $this->getContentTypeWithTaxonomy(),
                $this->getContentParameterKey() => $this->getContent(),
            ])
        );
    }

    /**
     * @return iterable<string,array{mixed,bool,bool}>
     */
    public function providerForEvaluateWithContent(): iterable
    {
        yield 'content with parent' => [
            $this->getContent(),
            true,
            true,
        ];

        yield 'content without parent' => [
            $this->getContent(),
            false,
            false,
        ];
    }

    public function testGetTemplateParameters(): void
    {
        $content = $this->getContent();

        $this->taxonomyServiceMock->method('loadEntryByContentId')->willReturn(
            $this->getTaxonomyEntry(false)
        );

        $templateParameters = $this->productsTab->getTemplateParameters([
            'content' => $content,
        ]);

        self::assertEquals(
            [
                $this->createMock(ProductInterface::class),
                $this->createMock(ProductInterface::class),
            ],
            $templateParameters['products']->getCurrentPageResults()->getProducts()
        );

        self::assertFalse($templateParameters['no_products']);

        self::assertEquals(
            $this->getTaxonomyEntry(false),
            $templateParameters['taxonomy_entry']
        );

        self::assertInstanceOf(FormView::class, $templateParameters['search_form']);
        self::assertInstanceOf(FormView::class, $templateParameters['form_taxonomy_products_assign']);
        self::assertInstanceOf(FormView::class, $templateParameters['form_taxonomy_products_unassign']);
    }

    public function getContentTypeParameterKey(): string
    {
        return 'contentType';
    }

    public function getContentParameterKey(): string
    {
        return 'content';
    }

    private function getContentTypeWithTaxonomy(): ContentType
    {
        return new ContentType([
            'id' => 1,
            'identifier' => 'test_identifier',
            'mainLanguageCode' => 'eng-GB',
        ]);
    }

    private function getContentTypeWithoutTaxonomy(): ContentType
    {
        return new ContentType([
            'id' => 1,
            'identifier' => 'foo',
            'mainLanguageCode' => 'eng-GB',
        ]);
    }

    private function getContent(): Content
    {
        return new Content([
            'versionInfo' => new VersionInfo(
                [
                    'contentInfo' => new ContentInfo(
                        [
                            'id' => self::TEST_CONTENT_ID,
                        ]
                    ),
                ]
            ),
            'contentType' => $this->getContentTypeWithTaxonomy(),
        ]);
    }

    private function getTaxonomyEntry(bool $withParent = true): TaxonomyEntry
    {
        return new TaxonomyEntry(
            123,
            'test_taxonomy_entry',
            'Test Taxonomy Entry',
            'eng-GB',
            [],
            $withParent ? $this->getParentTaxonomyEntry() : null,
            $this->getContent(),
            'taxonomy'
        );
    }

    private function getParentTaxonomyEntry(): TaxonomyEntry
    {
        return new TaxonomyEntry(
            1234,
            'test_taxonomy_entry_parent',
            'Parent Taxonomy Entry',
            'eng-GB',
            [],
            null,
            $this->getContent(),
            'taxonomy'
        );
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\ProductServiceInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createProductServiceMock(): ProductServiceInterface
    {
        $products = [
            $this->createMock(ProductInterface::class),
            $this->createMock(ProductInterface::class),
        ];

        $productList = $this->createMock(ProductListInterface::class);
        $productList->method('getProducts')->willReturn($products);
        $productList->method('getTotalCount')->willReturn(count($products));

        $productService = $this->createMock(ProductServiceInterface::class);
        $productService->method('findProducts')->willReturn($productList);

        return $productService;
    }

    /**
     * @return \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createConfigResolverMock(): ConfigResolverInterface
    {
        $configResolver = $this->createMock(ConfigResolverInterface::class);
        $configResolver->method('getParameter')->willReturn(10);

        return $configResolver;
    }

    /**
     * @return \Ibexa\Taxonomy\Service\TaxonomyConfiguration|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createTaxonomyConfigurationMock(): TaxonomyConfiguration
    {
        $taxonomyConfiguration = $this->createMock(TaxonomyConfiguration::class);
        $taxonomyConfiguration
            ->method('getConfigForTaxonomy')
            ->willReturn('test_identifier');

        $taxonomyConfiguration
            ->method('getTaxonomyForContentType')
            ->willReturn('product_categories');

        return $taxonomyConfiguration;
    }

    /**
     * @return \Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createTaxonomyServiceMock(): TaxonomyServiceInterface
    {
        return $this->createMock(TaxonomyServiceInterface::class);
    }

    /**
     * @return \Symfony\Component\Form\FormFactoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createFormFactoryMock(): FormFactoryInterface
    {
        $form = $this->createMock(FormInterface::class);
        $form->method('createView')->willReturn($this->createMock(FormView::class));

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->method('create')->willReturn($form);

        return $formFactory;
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\QueryTypeRegistryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createQueryTypeRegistryMock(): QueryTypeRegistryInterface
    {
        $queryTypeRegistry = $this->createMock(QueryTypeRegistryInterface::class);
        $queryTypeRegistry
            ->method('getQueryType')
            ->with('IbexaProductSearch')
            ->willReturn(
                new SearchQueryType()
            );

        return $queryTypeRegistry;
    }
}
