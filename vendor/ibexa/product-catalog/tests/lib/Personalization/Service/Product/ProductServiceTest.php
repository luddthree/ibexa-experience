<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace  Ibexa\Tests\ProductCatalog\Personalization\Service\Product;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Personalization\Config\Authentication\ParametersResolverInterface;
use Ibexa\Personalization\Config\ItemType\IncludedItemTypeResolverInterface;
use Ibexa\Personalization\Request\Item\AbstractPackage;
use Ibexa\Personalization\Request\Item\ItemIdsPackage;
use Ibexa\Personalization\Request\Item\PackageList;
use Ibexa\Personalization\Request\Item\UriPackage;
use Ibexa\Personalization\Service\Item\ItemServiceInterface;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Ibexa\Personalization\Value\Authentication\Parameters;
use Ibexa\Personalization\Value\Item\Action;
use Ibexa\ProductCatalog\Local\Repository\Values\Product;
use Ibexa\ProductCatalog\Local\Repository\Values\ProductType;
use Ibexa\ProductCatalog\Local\Repository\Values\ProductVariant;
use Ibexa\ProductCatalog\Personalization\ProductVariant\Routing\UrlGeneratorInterface;
use Ibexa\ProductCatalog\Personalization\Service\Product\ProductService;
use Ibexa\ProductCatalog\Personalization\Service\Product\ProductServiceInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Personalization\Service\Content\ContentService
 */
final class ProductServiceTest extends TestCase
{
    private const CUSTOMER_ID = 12345;
    private const LICENSE_KEY = '12345-12345-12345-12345';
    private const LANGUAGE_CODE = 'eng-GB';

    private ProductServiceInterface $productService;

    /** @var \Ibexa\Personalization\Config\Authentication\ParametersResolverInterface&\PHPUnit\Framework\MockObject\MockObject */
    private ParametersResolverInterface $parametersResolver;

    /** @var \Ibexa\Personalization\Config\ItemType\IncludedItemTypeResolverInterface&\PHPUnit\Framework\MockObject\MockObject */
    private IncludedItemTypeResolverInterface $includedItemTypeResolver;

    /** @var \Ibexa\Personalization\Service\Item\ItemServiceInterface&\PHPUnit\Framework\MockObject\MockObject */
    private ItemServiceInterface $itemService;

    /** @var \Ibexa\Personalization\Service\Setting\SettingServiceInterface&\PHPUnit\Framework\MockObject\MockObject */
    private SettingServiceInterface $settingService;

    /** @var \Ibexa\ProductCatalog\Personalization\ProductVariant\Routing\UrlGeneratorInterface&\PHPUnit\Framework\MockObject\MockObject */
    private UrlGeneratorInterface $urlGenerator;

    protected function setUp(): void
    {
        $this->parametersResolver = $this->createMock(ParametersResolverInterface::class);
        $this->itemService = $this->createMock(ItemServiceInterface::class);
        $this->settingService = $this->createMock(SettingServiceInterface::class);
        $this->includedItemTypeResolver = $this->createMock(IncludedItemTypeResolverInterface::class);
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);

        $this->productService = new ProductService(
            $this->parametersResolver,
            $this->itemService,
            $this->settingService,
            $this->includedItemTypeResolver,
            $this->urlGenerator
        );
    }

    /**
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function testUpdate(): void
    {
        $productVariant = $this->createProductVariant(1);
        $content = $this->getBaseContent($productVariant);
        $url = 'site.link.invalid/api/ibexa/v2/personalization/v1/product_variant/code/foo-1?lang=eng-GB';

        $this->mockSettingServiceCheckInstallationKey(true);
        $this->mockIncludedItemTypeResolverIsContentIncluded(
            [
                [$content, true],
            ]
        );
        $this->mockParametersResolverResolveForContent(
            [
                [$content, [self::LANGUAGE_CODE => $this->getAuthenticationParameters()]],
            ]
        );
        $this->mockUrlGeneratorGenerate($productVariant, $url);
        $this->mockItemService(
            Action::ACTION_UPDATE,
            $this->createPackageList(
                new UriPackage(
                    2,
                    'Product',
                    self::LANGUAGE_CODE,
                    $url
                ),
            )
        );

        $this->productService->updateVariant($productVariant);
    }

    /**
     * @dataProvider provideDataForTestUpdateVariants
     *
     * @param array<\Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface> $variants
     * @param array<array{
     *      \Ibexa\Contracts\Core\Repository\Values\Content\Content,
     *      bool
     * }> $includedItemTypeResolverMockValueMap
     * @param array<array{
     *      \Ibexa\Contracts\Core\Repository\Values\Content\Content,
     *      array<string, \Ibexa\Personalization\Value\Authentication\Parameters>,
     * }> $parametersResolverMockValueMap
     * @param array<array{
     *      array{int},
     *      string,
     *      string
     * }> $urlGeneratorValueMap
     *
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function testUpdateVariants(
        array $variants,
        array $includedItemTypeResolverMockValueMap,
        array $parametersResolverMockValueMap,
        array $urlGeneratorValueMap,
        PackageList $packageList
    ): void {
        $this->mockSettingServiceCheckInstallationKey(true);
        $this->mockIncludedItemTypeResolverIsContentIncluded($includedItemTypeResolverMockValueMap);
        $this->mockParametersResolverResolveForContent($parametersResolverMockValueMap);
        $this->mockUrlGeneratorGenerateForGenerateForContentIds($urlGeneratorValueMap);
        $this->mockItemService(Action::ACTION_UPDATE, $packageList);

        $this->productService->updateVariants($variants);
    }

    /**
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function testDelete(): void
    {
        $productVariant = $this->createProductVariant(1);
        $content = $this->getBaseContent($productVariant);

        $this->mockSettingServiceCheckInstallationKey(true);
        $this->mockIncludedItemTypeResolverIsContentIncluded(
            [
                [$content, true],
            ]
        );
        $this->mockParametersResolverResolveForContent(
            [
                [$content, [self::LANGUAGE_CODE => $this->getAuthenticationParameters()]],
            ]
        );
        $this->mockItemService(
            Action::ACTION_DELETE,
            $this->createPackageList(
                new ItemIdsPackage(
                    2,
                    'Product',
                    'eng-GB',
                    ['foo-1']
                ),
            )
        );

        $this->productService->deleteVariant($productVariant);
    }

    /**
     * @dataProvider provideDataForTestDeleteVariants
     *
     * @param array<\Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface> $variants
     * @param array<array{
     *      \Ibexa\Contracts\Core\Repository\Values\Content\Content,
     *      bool
     * }> $includedItemTypeResolverMockValueMap
     * @param array<array{
     *      \Ibexa\Contracts\Core\Repository\Values\Content\Content,
     *      array<string, \Ibexa\Personalization\Value\Authentication\Parameters>,
     * }> $parametersResolverMockValueMap
     *
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function testDeleteVariants(
        array $variants,
        array $includedItemTypeResolverMockValueMap,
        array $parametersResolverMockValueMap,
        PackageList $packageList
    ): void {
        $this->mockSettingServiceCheckInstallationKey(true);
        $this->mockIncludedItemTypeResolverIsContentIncluded($includedItemTypeResolverMockValueMap);
        $this->mockParametersResolverResolveForContent($parametersResolverMockValueMap);

        $this->mockItemService(
            Action::ACTION_DELETE,
            $packageList
        );

        $this->productService->deleteVariants($variants);
    }

    /**
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function testUpdateVariantsWithEmptyArray(): void
    {
        $this->mockParametersResolverResolveForContentWillBeNeverCalled();
        $this->mockItemServiceMethodWillBeNeverCalled(Action::ACTION_UPDATE);

        $this->productService->updateVariants([]);
    }

    /**
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function testDeleteVariantsWithEmptyArray(): void
    {
        $this->mockParametersResolverResolveForContentWillBeNeverCalled();
        $this->mockItemServiceMethodWillBeNeverCalled(Action::ACTION_DELETE);

        $this->productService->deleteVariants([]);
    }

    /**
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function testUpdateVariantInvalidInstallationKey(): void
    {
        $this->mockSettingServiceCheckInstallationKey(false);
        $this->mockParametersResolverResolveForContentWillBeNeverCalled();
        $this->mockItemServiceMethodWillBeNeverCalled(Action::ACTION_DELETE);

        $this->productService->updateVariant(
            $this->getVariantMock($this->createMock(Content::class))
        );
    }

    /**
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function testUpdateVariantsInvalidInstallationKey(): void
    {
        $this->mockSettingServiceCheckInstallationKey(false);
        $this->mockParametersResolverResolveForContentWillBeNeverCalled();
        $this->mockItemServiceMethodWillBeNeverCalled(Action::ACTION_DELETE);

        $this->productService->updateVariants(
            [
                $this->createMock(ProductVariantInterface::class),
            ]
        );
    }

    /**
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function testDeleteVariantInvalidInstallationKey(): void
    {
        $this->mockSettingServiceCheckInstallationKey(false);
        $this->mockParametersResolverResolveForContentWillBeNeverCalled();
        $this->mockItemServiceMethodWillBeNeverCalled(Action::ACTION_UPDATE);

        $this->productService->deleteVariant(
            $this->getVariantMock($this->createMock(Content::class))
        );
    }

    /**
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function testDeleteVariantsInvalidInstallationKey(): void
    {
        $this->mockSettingServiceCheckInstallationKey(false);
        $this->mockParametersResolverResolveForContentWillBeNeverCalled();
        $this->mockItemServiceMethodWillBeNeverCalled(Action::ACTION_DELETE);

        $this->productService->deleteVariants(
            [
                $this->createMock(ProductVariantInterface::class),
            ]
        );
    }

    /**
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function testUpdateNotIncludedVariant(): void
    {
        $variant = $this->createProductVariant(1);

        $this->mockSettingServiceCheckInstallationKey(true);
        $this->mockIncludedItemTypeResolverIsContentIncluded(
            [
                [$this->getBaseContent($variant), false],
            ]
        );
        $this->mockParametersResolverResolveForContentWillBeNeverCalled();
        $this->mockItemServiceMethodWillBeNeverCalled(Action::ACTION_UPDATE);

        $this->productService->updateVariant($variant);
    }

    /**
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function testUpdateNotIncludedVariants(): void
    {
        $variant = $this->createProductVariant(1);
        $this->mockSettingServiceCheckInstallationKey(true);
        $this->mockIncludedItemTypeResolverIsContentIncluded(
            [
                [$this->getBaseContent($variant), false],
            ]
        );
        $this->mockParametersResolverResolveForContentWillBeNeverCalled();
        $this->mockItemServiceMethodWillBeNeverCalled(Action::ACTION_UPDATE);

        $this->productService->updateVariants([$variant]);
    }

    /**
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function testDeleteNotIncludedVariant(): void
    {
        $variant = $this->createProductVariant(1);
        $this->mockSettingServiceCheckInstallationKey(true);
        $this->mockIncludedItemTypeResolverIsContentIncluded(
            [
                [$this->getBaseContent($variant), false],
            ]
        );
        $this->mockParametersResolverResolveForContentWillBeNeverCalled();
        $this->mockItemServiceMethodWillBeNeverCalled(Action::ACTION_DELETE);

        $this->productService->deleteVariant($variant);
    }

    /**
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function testDeleteNotIncludedVariants(): void
    {
        $variant = $this->createProductVariant(1);
        $this->mockSettingServiceCheckInstallationKey(true);
        $this->mockIncludedItemTypeResolverIsContentIncluded(
            [
                [$this->getBaseContent($variant), false],
            ]
        );
        $this->mockParametersResolverResolveForContentWillBeNeverCalled();
        $this->mockItemServiceMethodWillBeNeverCalled(Action::ACTION_DELETE);

        $this->productService->deleteVariants([$variant]);
    }

    /**
     * @return iterable<array{
     *      array<\Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface>,
     *      array<array{
     *          \Ibexa\Contracts\Core\Repository\Values\Content\Content,
     *          bool
     *      }>,
     *      array<array{
     *          \Ibexa\Contracts\Core\Repository\Values\Content\Content,
     *          array<string, \Ibexa\Personalization\Value\Authentication\Parameters>
     *      }>,
     *      array<array{
     *          array{string},
     *          string,
     *          string
     *      }>,
     *      \Ibexa\Personalization\Request\Item\PackageList
     * }>
     */
    public function provideDataForTestUpdateVariants(): iterable
    {
        $variant = $this->createProductVariant(1);
        $variant2 = $this->createProductVariant(2);
        $content = $this->getBaseContent($variant);
        $content2 = $this->getBaseContent($variant2);

        $url = 'shop.link.invalid/api/ibexa/v2/personalization/v1/product_variant/code/list/foo-1,foo-2?lang=eng-GB';

        yield 'Variants related to the same content type' => [
            [
                $variant,
                $variant2,
            ],
            [
                [$content, true],
                [$content2, true],
            ],
            [
                [$content, [self::LANGUAGE_CODE => $this->getAuthenticationParameters()]],
                [$content2, [self::LANGUAGE_CODE => $this->getAuthenticationParameters()]],
            ],
            [
                [
                    ['foo-1', 'foo-2'], 'eng-GB', $url,
                ],
            ],
            $this->createPackageList(
                new UriPackage(
                    2,
                    'Product',
                    self::LANGUAGE_CODE,
                    $url
                ),
            ),
        ];
    }

    /**
     * @return iterable<array{
     *      array<\Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface>,
     *      array<array{
     *          \Ibexa\Contracts\Core\Repository\Values\Content\Content,
     *          bool
     *      }>,
     *      array<array{
     *          \Ibexa\Contracts\Core\Repository\Values\Content\Content,
     *          array<string, \Ibexa\Personalization\Value\Authentication\Parameters>
     *      }>,
     *      \Ibexa\Personalization\Request\Item\PackageList
     * }>
     */
    public function provideDataForTestDeleteVariants(): iterable
    {
        $variant = $this->createProductVariant(1);
        $variant2 = $this->createProductVariant(2);
        $content = $this->getBaseContent($variant);
        $content2 = $this->getBaseContent($variant2);

        yield 'Variants related to the same content type' => [
            [
                $variant,
                $variant2,
            ],
            [
                [$content, true],
                [$content2, true],
            ],
            [
                [$content, [self::LANGUAGE_CODE => $this->getAuthenticationParameters()]],
                [$content2, [self::LANGUAGE_CODE => $this->getAuthenticationParameters()]],
            ],
            $this->createPackageList(
                new ItemIdsPackage(
                    2,
                    'Product',
                    'eng-GB',
                    ['foo-1', 'foo-2']
                ),
            ),
        ];
    }

    /**
     * @param array<array{
     *     \Ibexa\Contracts\Core\Repository\Values\Content\Content,
     *     array<string, \Ibexa\Personalization\Value\Authentication\Parameters>,
     * }> $valueMap
     */
    private function mockParametersResolverResolveForContent(array $valueMap): void
    {
        $this->parametersResolver
            ->expects(self::atLeastOnce())
            ->method('resolveAllForContent')
            ->willReturnMap($valueMap);
    }

    private function mockParametersResolverResolveForContentWillBeNeverCalled(): void
    {
        $this->parametersResolver
            ->expects(self::never())
            ->method('resolveAllForContent');
    }

    private function mockUrlGeneratorGenerate(
        ProductVariantInterface $productVariant,
        string $url
    ): void {
        $this->urlGenerator
            ->expects(self::once())
            ->method('generate')
            ->with($productVariant)
            ->willReturn($url);
    }

    private function mockSettingServiceCheckInstallationKey(bool $installationKeyFound): void
    {
        $this->settingService
            ->expects(self::once())
            ->method('isInstallationKeyFound')
            ->willReturn($installationKeyFound);
    }

    /**
     * @param array<array{
     *     \Ibexa\Contracts\Core\Repository\Values\Content\Content,
     *     bool
     * }> $valueMap
     */
    private function mockIncludedItemTypeResolverIsContentIncluded(array $valueMap): void
    {
        $this->includedItemTypeResolver
            ->expects(self::atLeastOnce())
            ->method('isContentIncluded')
            ->willReturnMap($valueMap);
    }

    /**
     * @param array<array{
     *      array{int},
     *      string,
     *      string
     * }> $valueMap
     */
    private function mockUrlGeneratorGenerateForGenerateForContentIds(array $valueMap): void
    {
        $this->urlGenerator
            ->expects(self::atLeastOnce())
            ->method('generateForVariantCodes')
            ->willReturnMap($valueMap);
    }

    /**
     * @phpstan-param \Ibexa\Personalization\Value\Item\Action::ACTION_* $action
     */
    private function mockItemService(
        string $action,
        PackageList $packageList
    ): void {
        $this->itemService
            ->expects(self::once())
            ->method($action)
            ->with(
                $this->getAuthenticationParameters(),
                $packageList
            );
    }

    /**
     * @phpstan-param \Ibexa\Personalization\Value\Item\Action::ACTION_* $action
     */
    private function mockItemServiceMethodWillBeNeverCalled(string $action): void
    {
        $this->itemService
            ->expects(self::never())
            ->method($action);
    }

    private function getAuthenticationParameters(): Parameters
    {
        return new Parameters(
            self::CUSTOMER_ID,
            self::LICENSE_KEY
        );
    }

    private function createProductVariant(int $contentId): ProductVariantInterface
    {
        return new ProductVariant(
            new Product(
                new ProductType(
                    $this->createProductContentTypeMock()
                ),
                $this->createBaseContentMock(),
                'base-product'
            ),
            'foo-' . $contentId
        );
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType&\PHPUnit\Framework\MockObject\MockObject
     */
    private function createProductContentTypeMock(): ContentType
    {
        $contentType = $this->createMock(ContentType::class);
        $contentType
            ->method('__get')
            ->with('id')
            ->willReturn(2);

        $contentType
            ->method('getName')
            ->with(self::LANGUAGE_CODE)
            ->willReturn('Product');

        return $contentType;
    }

    private function createPackageList(AbstractPackage ...$package): PackageList
    {
        return new PackageList($package);
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private function getVariantMock(Content $content): ProductVariantInterface
    {
        $baseProduct = $this->createMock(ContentAwareProductInterface::class);
        $baseProduct
            ->method('getContent')
            ->willReturn($content);

        $variant = $this->createMock(ProductVariantInterface::class);
        $variant
            ->method('getBaseProduct')
            ->willReturn($baseProduct);

        return $variant;
    }

    /**
     *  @return \Ibexa\Contracts\Core\Repository\Values\Content\Content&\PHPUnit\Framework\MockObject\MockObject
     */
    private function createBaseContentMock(): Content
    {
        $contentType = $this->createProductContentTypeMock();
        $content = $this->createMock(Content::class);
        $content
            ->method('getContentType')
            ->willReturn($contentType);

        $language = $this->createMock(Language::class);
        $language
            ->method('getLanguageCode')
            ->willReturn(self::LANGUAGE_CODE);

        $versionInfo = $this->createMock(VersionInfo::class);
        $versionInfo
            ->method('getLanguages')
            ->willReturn([$language]);

        $content
            ->method('getVersionInfo')
            ->willReturn($versionInfo);

        return $content;
    }

    private function getBaseContent(ProductVariantInterface $variant): Content
    {
        /** @var \Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface $baseProduct */
        $baseProduct = $variant->getBaseProduct();

        return $baseProduct->getContent();
    }
}
