<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Behat\Component;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\Dialog;
use Ibexa\AdminUi\Behat\Component\IbexaDropdown;
use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Criterion\ChildElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

final class GenerateProductVariantsPopupWindow extends Component
{
    private Dialog $dialog;

    private IbexaDropdown $ibexaDropdown;

    public function __construct(Session $session, Dialog $dialog, IbexaDropdown $ibexaDropdown)
    {
        parent::__construct($session);
        $this->dialog = $dialog;
        $this->ibexaDropdown = $ibexaDropdown;
    }

    /**
     * @param array<string,string> $value
     */
    public function fillProductVariantAttributeInputValues(string $label, array $value): void
    {
        $this->getHTMLPage()->setTimeout(5)->findAll($this->getLocator('fieldsForm'))
            ->getByCriterion(new ChildElementTextCriterion($this->getLocator('fieldLabel'), $label))
            ->find($this->getLocator('field'))
            ->setValue($value['value']);
    }

    /**
     * @param array<string,string> $value
     */
    public function selectProductVariantAttributeDropdownValues(string $label, array $value): void
    {
        $this->getHTMLPage()->setTimeout(5)->findAll($this->getLocator('fieldsForm'))
            ->getByCriterion(new ChildElementTextCriterion($this->getLocator('fieldLabel'), $label))
            ->find($this->getLocator('field'))
            ->click();

        $this->ibexaDropdown->verifyIsLoaded();
        $this->ibexaDropdown->selectOption($value['value']);
    }

    public function confirmGeneratingProductVariants(): void
    {
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function cancelGeneratingProductVariants(): void
    {
        $this->dialog->verifyIsLoaded();
        $this->dialog->decline();
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('generateVariantsPopupWindowTitle'))->assert()->textEquals('Generate variants');
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('generateVariantsPopupWindowTitle', '.modal-title'),
            new VisibleCSSLocator('fieldsForm', '.ibexa-table__row'),
            new VisibleCSSLocator('fieldLabel', '.ibexa-label'),
            new VisibleCSSLocator('field', 'div.ibexa-generate-variants__form-field input, div.ibexa-generate-variants__form-field .ibexa-dropdown'),
        ];
    }
}
