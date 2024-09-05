<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Behat\Component\Fields;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\Fields\FieldTypeComponent;
use Ibexa\AdminUi\Behat\Component\IbexaDropdown;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

final class CustomerGroup extends FieldTypeComponent
{
    private IbexaDropdown $ibexaDropdown;

    public function __construct(Session $session, IbexaDropdown $ibexaDropdown)
    {
        parent::__construct($session);
        $this->ibexaDropdown = $ibexaDropdown;
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('dropdown', '.ibexa-dropdown__selection-info'),
        ];
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'ibexa_customer_group';
    }

    /**
     * @param array<string,string> $parameters
     */
    public function setValue(array $parameters): void
    {
        $this->getHTMLPage()->find($this->parentLocator)->find($this->getLocator('dropdown'))->click();

        $this->ibexaDropdown->verifyIsLoaded();
        $this->ibexaDropdown->selectOption($parameters['value']);
    }
}
