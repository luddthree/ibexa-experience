<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Behat\Page;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\Table\TableBuilder;
use Ibexa\AdminUi\Behat\Component\Table\TableInterface;
use Ibexa\Behat\Browser\Element\Criterion\ChildElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Routing\Router;
use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductType\ProductTypeQuery;
use Webmozart\Assert\Assert;

final class ProductTypePage extends Page
{
    private string $expectedProductTypeName;

    private TableInterface $fieldTable;

    private TableInterface $attributeTable;

    private TableInterface $vatRateTable;

    private string $expectedProductTypeIdentifier;

    private ProductTypeServiceInterface $productTypeService;

    public function __construct(
        Session $session,
        Router $router,
        TableBuilder $tableBuilder,
        ProductTypeServiceInterface $productTypeService
    ) {
        parent::__construct($session, $router);
        $this->fieldTable = $tableBuilder->newTable()
             ->withParentLocator(new VisibleCSSLocator('contentFieldsTable', 'section.ibexa-fieldgroup:nth-of-type(1)'))
             ->build();
        $this->attributeTable = $tableBuilder->newTable()
            ->withParentLocator(new VisibleCSSLocator('attributesTable', 'section.ibexa-fieldgroup:nth-of-type(2)'))
            ->build();
        $this->vatRateTable = $tableBuilder->newTable()
            ->withParentLocator(new VisibleCSSLocator('vatRatesTable', 'section.ibexa-fieldgroup:nth-of-type(3)'))
            ->build();
        $this->productTypeService = $productTypeService;
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()
            ->find($this->getLocator('pageTitle'))
            ->assert()->textEquals($this->expectedProductTypeName);
    }

    public function setExpectedProductTypeName(string $productTypeName): void
    {
        $this->expectedProductTypeName = $productTypeName;
        $productTypes = $this->productTypeService->findProductTypes(new ProductTypeQuery($productTypeName))->getProductTypes();
        Assert::notEmpty($productTypes, 'Product Type was not found');
        $productType = $productTypes[0];
        Assert::eq($productType->getName(), $productTypeName);
        $this->expectedProductTypeIdentifier = $productType->getIdentifier();
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
     * @param array<string,string> $fieldTypeData
     */
    public function hasFieldType(array $fieldTypeData): bool
    {
        return $this->fieldTable->hasElement($fieldTypeData);
    }

    /**
     * @param array<string,string> $attributeData
     */
    public function hasAttribute(array $attributeData): bool
    {
        $this->getHTMLPage()->find($this->getLocator('scrollableContainer'))->scrollToBottom($this->getSession());

        return $this->attributeTable->hasElement($attributeData);
    }

    /**
     * @param array<string,string> $vatRateData
     */
    public function hasVATRates(array $vatRateData): bool
    {
        $this->getHTMLPage()->find($this->getLocator('scrollableContainer'))->scrollToBottom($this->getSession());

        return $this->vatRateTable->hasElement($vatRateData);
    }

    public function getName(): string
    {
        return 'Product Type';
    }

    protected function getRoute(): string
    {
        return sprintf(
            '/product-type/view/%s',
            $this->expectedProductTypeIdentifier
        );
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('pageTitle', '.ibexa-page-title__title'),
            new VisibleCSSLocator('globalPropertiesItem', '.ibexa-details__item'),
            new VisibleCSSLocator('globalPropertiesLabel', '.ibexa-details__item-label'),
            new VisibleCSSLocator('globalPropertiesValue', '.ibexa-details__item-content'),
            new VisibleCSSLocator('scrollableContainer', '.ibexa-back-to-top-scroll-container'),
        ];
    }
}
