<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Behat\Page;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\ContentActionsMenu;
use Ibexa\AdminUi\Behat\Component\IbexaDropdown;
use Ibexa\AdminUi\Behat\Page\AdminUpdateItemPage;
use Ibexa\Behat\Browser\Element\Criterion\ChildElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Routing\Router;

final class AttributeEditPage extends AdminUpdateItemPage
{
    private IbexaDropdown $ibexaDropdown;

    public function __construct(
        Session $session,
        Router $router,
        ContentActionsMenu $contentActionsMenu,
        IbexaDropdown $ibexaDropdown
    ) {
        parent::__construct($session, $router, $contentActionsMenu);
        $this->ibexaDropdown = $ibexaDropdown;
    }

    public function selectAttributeGroup(string $attributeGroupName): void
    {
        $this->getHTMLPage()
            ->findAll($this->getLocator('formField'))
            ->getByCriterion(
                new ChildElementTextCriterion(
                    $this->getLocator('formLabel'),
                    'Attribute group'
                )
            )
            ->find($this->getLocator('dropdown'))
            ->click();

        $this->ibexaDropdown->verifyIsLoaded();
        $this->ibexaDropdown->selectOption($attributeGroupName);
    }

    protected function specifyLocators(): array
    {
        return array_merge(
            [
                new VisibleCSSLocator('formField', '.ibexa-pc-edit__form-field'),
                new VisibleCSSLocator('formLabel', '.ibexa-label'),
                new VisibleCSSLocator('dropdown', '.ibexa-dropdown__wrapper'),
            ],
            parent::specifyLocators()
        );
    }
}
