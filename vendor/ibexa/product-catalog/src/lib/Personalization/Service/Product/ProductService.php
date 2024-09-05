<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Personalization\Service\Product;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Personalization\Content\AbstractContentService;
use Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface;
use Ibexa\Personalization\Config\Authentication\ParametersResolverInterface;
use Ibexa\Personalization\Config\ItemType\IncludedItemTypeResolverInterface;
use Ibexa\Personalization\Service\Item\ItemServiceInterface;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Ibexa\Personalization\Value\Authentication\Parameters;
use Ibexa\ProductCatalog\Local\Repository\Values\ProductVariant;
use Ibexa\ProductCatalog\Personalization\ProductVariant\Routing\UrlGeneratorInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Webmozart\Assert\Assert;

/**
 * @internal
 */
final class ProductService extends AbstractContentService implements ProductServiceInterface
{
    use LoggerAwareTrait;

    private SettingServiceInterface $settingService;

    private IncludedItemTypeResolverInterface $includedItemTypeResolver;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        ParametersResolverInterface $parametersResolver,
        ItemServiceInterface $itemService,
        SettingServiceInterface $settingService,
        IncludedItemTypeResolverInterface $includedItemTypeResolver,
        UrlGeneratorInterface $urlGenerator,
        ?LoggerInterface $logger = null
    ) {
        parent::__construct(
            $settingService,
            $includedItemTypeResolver,
            $itemService,
            $parametersResolver
        );
        $this->settingService = $settingService;
        $this->includedItemTypeResolver = $includedItemTypeResolver;
        $this->urlGenerator = $urlGenerator;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function deleteVariant(ProductVariantInterface $productVariant): void
    {
        $baseContent = $this->getBaseContent($productVariant);
        if (!$this->canProcessContentItem($baseContent)) {
            return;
        }

        $authenticationParameters = $this->getAuthenticationParameters($baseContent);
        if (empty($authenticationParameters)) {
            return;
        }

        $deleteData = [];
        foreach ($this->getLanguageCodesFromVersionInfo($baseContent->getVersionInfo()) as $languageCode) {
            if (!isset($authenticationParameters[$languageCode])) {
                continue;
            }

            $authenticationParametersForLanguage = $authenticationParameters[$languageCode];
            $customerId = $authenticationParametersForLanguage->getCustomerId();
            $deleteData[$customerId] = $this->prepareDeleteData(
                $authenticationParametersForLanguage,
                $baseContent->getContentType(),
                [$productVariant],
                $languageCode,
            );
        }

        $this->delete($deleteData);
    }

    /**
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function updateVariant(ProductVariantInterface $productVariant): void
    {
        $baseContent = $this->getBaseContent($productVariant);
        if (!$this->canProcessContentItem($baseContent)) {
            return;
        }

        $authenticationParameters = $this->getAuthenticationParameters($baseContent);
        if (empty($authenticationParameters)) {
            return;
        }

        $updateData = [];
        foreach ($this->getLanguageCodesFromVersionInfo($baseContent->getVersionInfo()) as $languageCode) {
            if (!isset($authenticationParameters[$languageCode])) {
                continue;
            }

            $authenticationParametersForLanguage = $authenticationParameters[$languageCode];
            $customerId = $authenticationParametersForLanguage->getCustomerId();
            $updateData[$customerId] = $this->prepareUpdateData(
                $authenticationParametersForLanguage,
                $baseContent->getContentType(),
                $languageCode,
                $this->urlGenerator->generate(
                    $productVariant,
                    $languageCode
                )
            );
        }

        $this->update($updateData);
    }

    /**
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function updateVariants(array $productVariants): void
    {
        if (!$this->canProcessVariants($productVariants)) {
            return;
        }

        $baseProductVariant = $productVariants[0];
        $baseContent = $this->getBaseContent($baseProductVariant);
        $authenticationParameters = $this->getAuthenticationParameters($baseContent);
        if (empty($authenticationParameters)) {
            return;
        }

        $updateData = [];
        foreach ($this->getLanguageCodesFromVersionInfo($baseContent->getVersionInfo()) as $languageCode) {
            if (!isset($authenticationParameters[$languageCode])) {
                continue;
            }

            $authenticationParametersForLanguage = $authenticationParameters[$languageCode];
            $customerId = $authenticationParametersForLanguage->getCustomerId();
            $updateData[$customerId] = $this->prepareUpdateData(
                $authenticationParametersForLanguage,
                $baseContent->getContentType(),
                $languageCode,
                $this->urlGenerator->generateForVariantCodes(
                    $this->getVariantCodes($productVariants),
                    $languageCode
                )
            );
        }

        $this->update($updateData);
    }

    /**
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function deleteVariants(array $productVariants): void
    {
        if (!$this->canProcessVariants($productVariants)) {
            return;
        }

        $baseProductVariant = $productVariants[0];
        $deletedBaseContent = $this->getBaseContent($baseProductVariant);
        $authenticationParameters = $this->getAuthenticationParameters($deletedBaseContent);
        if (empty($authenticationParameters)) {
            return;
        }

        $deleteData = [];
        foreach ($this->getLanguageCodesFromVersionInfo($deletedBaseContent->getVersionInfo()) as $languageCode) {
            $authenticationParametersForLanguage = $authenticationParameters[$languageCode];
            $customerId = $authenticationParametersForLanguage->getCustomerId();
            $deleteData[$customerId] = $this->prepareDeleteData(
                $authenticationParametersForLanguage,
                $deletedBaseContent->getContentType(),
                $productVariants,
                $languageCode,
            );
        }

        $this->delete($deleteData);
    }

    /**
     * @return array{
     *     authenticationParameters: \Ibexa\Personalization\Value\Authentication\Parameters,
     *     packageList: array<\Ibexa\Personalization\Request\Item\AbstractPackage>,
     * }
     */
    private function prepareUpdateData(
        Parameters $parameters,
        ContentType $contentType,
        string $languageCode,
        string $url
    ): array {
        return [
            'authenticationParameters' => $parameters,
            'packageList' => [
                $this->createUriPackage(
                    $contentType,
                    $languageCode,
                    $url
                ),
            ],
        ];
    }

    /**
     * @param array<\Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface> $productVariants
     *
     * @return array{
     *     authenticationParameters: \Ibexa\Personalization\Value\Authentication\Parameters,
     *     packageList: array<\Ibexa\Personalization\Request\Item\AbstractPackage>,
     * }
     */
    private function prepareDeleteData(
        Parameters $parameters,
        ContentType $contentType,
        array $productVariants,
        string $languageCode
    ): array {
        return [
            'authenticationParameters' => $parameters,
            'packageList' => [
                $this->createItemIdsPackage(
                    $contentType,
                    $this->getVariantCodes($productVariants),
                    $languageCode,
                ),
            ],
        ];
    }

    /**
     * @param array<\Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface> $productVariants
     *
     * @return array<string>
     */
    private function getVariantCodes(array $productVariants): array
    {
        $variantCodes = [];
        foreach ($productVariants as $productVariant) {
            if (!$productVariant instanceof ProductVariant) {
                continue;
            }

            $variantCodes[] = $productVariant->getCode();
        }

        return $variantCodes;
    }

    /**
     * @param array<\Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface> $variants
     */
    private function canProcessVariants(array $variants): bool
    {
        if (!$this->settingService->isInstallationKeyFound()) {
            return false;
        }

        if (empty($variants)) {
            $this->logArrayWithVariantsIsEmpty();

            return false;
        }

        Assert::allIsInstanceOf($variants, ProductVariant::class);

        if (!$this->includedItemTypeResolver->isContentIncluded($this->getBaseContent($variants[0]))) {
            return false;
        }

        return true;
    }

    private function logArrayWithVariantsIsEmpty(): void
    {
        $this->logger->error('Provided array with variants is empty');
    }

    private function getBaseContent(ProductVariantInterface $variant): Content
    {
        /** @var \Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface $baseProduct */
        $baseProduct = $variant->getBaseProduct();

        return $baseProduct->getContent();
    }
}
