<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Behat\Page;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\Breadcrumb;
use Ibexa\AdminUi\Behat\Component\ContentActionsMenu;
use Ibexa\AdminUi\Behat\Component\ContentItemAdminPreview;
use Ibexa\AdminUi\Behat\Component\ContentTypePicker;
use Ibexa\AdminUi\Behat\Component\Dialog;
use Ibexa\AdminUi\Behat\Component\IbexaDropdown;
use Ibexa\AdminUi\Behat\Component\LanguagePicker;
use Ibexa\AdminUi\Behat\Component\SubItemsList;
use Ibexa\AdminUi\Behat\Component\TranslationDialog;
use Ibexa\AdminUi\Behat\Component\UniversalDiscoveryWidget;
use Ibexa\AdminUi\Behat\Component\UpperMenu;
use Ibexa\AdminUi\Behat\Page\ContentViewPage;
use Ibexa\Behat\Browser\Element\Criterion\ChildElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Routing\Router;
use Ibexa\Behat\Core\Behat\ArgumentParser;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductName;
use Ibexa\ProductCatalog\Behat\Component\ProductPreview;
use Webmozart\Assert\Assert;

final class ProductPage extends ContentViewPage
{
    private string $expectedProductName;

    private string $expectedProductCode;

    private ProductServiceInterface $productService;

    private ProductPreview $productPreview;

    public function __construct(
        Session $session,
        Router $router,
        ContentActionsMenu $contentActionsMenu,
        SubItemsList $subItemList,
        ContentTypePicker $contentTypePicker,
        LanguagePicker $languagePicker,
        Dialog $dialog,
        TranslationDialog $translationDialog,
        Repository $repository,
        Breadcrumb $breadcrumb,
        ContentItemAdminPreview $contentItemAdminPreview,
        ArgumentParser $argumentParser,
        UniversalDiscoveryWidget $universalDiscoveryWidget,
        IbexaDropdown $ibexaDropdown,
        UpperMenu $upperMenu,
        ProductServiceInterface $productService,
        ProductPreview $productPreview
    ) {
        parent::__construct(
            $session,
            $router,
            $contentActionsMenu,
            $subItemList,
            $contentTypePicker,
            $languagePicker,
            $dialog,
            $translationDialog,
            $repository,
            $breadcrumb,
            $contentItemAdminPreview,
            $argumentParser,
            $universalDiscoveryWidget,
            $ibexaDropdown,
            $upperMenu
        );
        $this->productService = $productService;
        $this->productPreview = $productPreview;
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()
            ->find($this->getLocator('pageTitle'))
            ->assert()->textContains($this->expectedProductName);
    }

    public function setExpectedProductName(string $productName): void
    {
        $this->expectedProductName = $productName;
        $query = new ProductQuery(new ProductName($productName));
        $products = $this->productService->findProducts($query)->getProducts();
        Assert::notEmpty($products, 'Product was not found');
        $product = $products[0];
        Assert::eq($product->getName(), $productName);
        $this->expectedProductCode = $product->getCode();
    }

    public function setExpectedProductVariantCode(string $productVariantCode): void
    {
        $productVariant = $this->productService->getProductVariant($productVariantCode);
        $this->expectedProductCode = $productVariantCode;
        $this->expectedProductName = $productVariant->getName();
    }

    public function hasProperty(string $label, string $value): bool
    {
        return $this->getHTMLPage()
                ->findAll($this->getLocator('globalPropertiesItem'))
                ->getByCriterion(new ChildElementTextCriterion($this->getLocator('globalPropertiesLabel'), $label))
                ->find($this->getLocator('globalPropertiesValue'))
                ->getText() === $value;
    }

    /**
     * @param array<string,string> $expectedFieldValues
     */
    public function verifyFieldHasValues(string $fieldLabel, array $expectedFieldValues, ?string $type): void
    {
        $this->productPreview->verifyFieldHasValues($fieldLabel, $expectedFieldValues, $type);
    }

    public function getName(): string
    {
        return 'Product';
    }

    protected function getRoute(): string
    {
        return sprintf(
            '/product/%s',
            $this->expectedProductCode
        );
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('pageTitle', '.ibexa-page-title__title'),
            new VisibleCSSLocator('globalPropertiesItem', '.ibexa-details__item'),
            new VisibleCSSLocator('globalPropertiesLabel', '.ibexa-details__item-label'),
            new VisibleCSSLocator('globalPropertiesValue', '.ibexa-details__item-content'),
        ];
    }
}
