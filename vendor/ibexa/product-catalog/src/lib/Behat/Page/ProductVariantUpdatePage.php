<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Behat\Page;

use Behat\Mink\Session;
use Exception;
use Ibexa\AdminUi\Behat\Component\IbexaDropdown;
use Ibexa\Behat\Browser\Element\Criterion\ChildElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Routing\Router;

final class ProductVariantUpdatePage extends Page
{
    private IbexaDropdown $ibexaDropdown;

    public function __construct(Session $session, Router $router, IbexaDropdown $ibexaDropdown)
    {
        parent::__construct($session, $router);
        $this->ibexaDropdown = $ibexaDropdown;
    }

    /**
     * @param array<string,string> $value
     */
    public function fillProductVariantInputFields(string $label, array $value): void
    {
        $this->getHTMLPage()->setTimeout(5)->findAll($this->getLocator('fieldsForm'))
            ->getByCriterion(new ChildElementTextCriterion($this->getLocator('fieldLabel'), $label))
            ->find($this->getLocator('field'))
            ->setValue($value['value']);
    }

    /**
     * @param array<string,string> $value
     */
    public function selectProductVariantDropdownFields(string $label, array $value): void
    {
        $this->getHTMLPage()->setTimeout(5)->findAll($this->getLocator('fieldsForm'))
            ->getByCriterion(new ChildElementTextCriterion($this->getLocator('fieldLabel'), $label))
            ->find($this->getLocator('field'))
            ->click();

        $this->ibexaDropdown->verifyIsLoaded();
        $this->ibexaDropdown->selectOption($value['value']);
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->find($this->getLocator('pageHeader'))->assert()->isVisible();
    }

    public function getName(): string
    {
        return 'Product Variant Update';
    }

    protected function getRoute(): string
    {
        throw new Exception('not implemented yet');
    }

    public function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('fieldsForm', 'div#product_variant_update_attributes div, div#product_variant_create_attributes div'),
            new VisibleCSSLocator('fieldLabel', 'div .ibexa-label'),
            new VisibleCSSLocator('field', 'div input, div.ibexa-dropdown'),
            new VisibleCSSLocator('pageHeader', '.ibexa-edit-header__action-name'),
        ];
    }
}
