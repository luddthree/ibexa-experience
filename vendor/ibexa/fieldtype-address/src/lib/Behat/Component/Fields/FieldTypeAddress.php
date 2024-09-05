<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypeAddress\Behat\Component\Fields;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\Fields\FieldTypeComponent;
use Ibexa\AdminUi\Behat\Component\IbexaDropdown;
use Ibexa\Behat\Browser\Element\Condition\ElementNotExistsCondition;
use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

final class FieldTypeAddress extends FieldTypeComponent
{
    private const RELOAD_CLASS = 'ibexa-selenium-before-reload';

    private IbexaDropdown $ibexaDropdown;

    public function __construct(Session $session, IbexaDropdown $ibexaDropdown)
    {
        parent::__construct($session);
        $this->ibexaDropdown = $ibexaDropdown;
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('name', '#ezplatform_content_forms_content_edit_fieldsData_billing_address_value_name'),
            new VisibleCSSLocator('countryDropdown', '.ibexa-dropdown__selection-info'),
            new VisibleCSSLocator('firstName', '#ezplatform_content_forms_content_edit_fieldsData_billing_address_value_fields_first_name'),
            new VisibleCSSLocator('lastName', '#ezplatform_content_forms_content_edit_fieldsData_billing_address_value_fields_last_name'),
            new VisibleCSSLocator('taxId', '#ezplatform_content_forms_content_edit_fieldsData_billing_address_value_fields_tax_id'),
            new VisibleCSSLocator('region', '#ezplatform_content_forms_content_edit_fieldsData_billing_address_value_fields_region'),
            new VisibleCSSLocator('city', '#ezplatform_content_forms_content_edit_fieldsData_billing_address_value_fields_locality'),
            new VisibleCSSLocator('street', '#ezplatform_content_forms_content_edit_fieldsData_billing_address_value_fields_street'),
            new VisibleCSSLocator('postalCode', '#ezplatform_content_forms_content_edit_fieldsData_billing_address_value_fields_postal_code'),
            new VisibleCSSLocator('email', '#ezplatform_content_forms_content_edit_fieldsData_billing_address_value_fields_email'),
            new VisibleCSSLocator('phoneNumber', '#ezplatform_content_forms_content_edit_fieldsData_billing_address_value_fields_phone_number'),
        ];
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'ibexa_address';
    }

    /**
     * @param array<string,string> $parameters
     */
    public function setValue(array $parameters): void
    {
        $script = sprintf("document.querySelector('%s %s').click()", $this->parentLocator->getSelector(), $this->getLocator('countryDropdown')->getSelector());
        $this->getHTMLPage()->executeJavaScript($script);
        $this->ibexaDropdown->verifyIsLoaded();

        // Rework this once https://issues.ibexa.co/browse/IBX-6905 is fixed
        $this->addClass(
            $this->getHTMLPage()->find($this->parentLocator)->find($this->getLocator('region')),
            self::RELOAD_CLASS
        );
        $this->ibexaDropdown->selectOption($parameters['Country']);

        $this->getHTMLPage()->setTimeout(5)->waitUntilCondition(
            new ElementNotExistsCondition(
                $this->getHTMLPage(),
                new VisibleCSSLocator('field-reload', sprintf('.%s', self::RELOAD_CLASS))
            )
        );

        $this->setSpecificFieldValue('name', $parameters['Name']);
        if (isset($parameters['First name'])) {
            $this->setSpecificFieldValue('firstName', $parameters['First name']);
            $this->setSpecificFieldValue('lastName', $parameters['Last name']);
            $this->setSpecificFieldValue('taxId', $parameters['Tax ID']);
        }
        $this->setSpecificFieldValue('region', $parameters['Region']);
        $this->setSpecificFieldValue('city', $parameters['City']);
        $this->setSpecificFieldValue('street', $parameters['Street']);
        $this->setSpecificFieldValue('postalCode', $parameters['Postal Code']);
        $this->setSpecificFieldValue('email', $parameters['Email']);
        $this->setSpecificFieldValue('phoneNumber', $parameters['Phone']);
    }

    private function setSpecificFieldValue(string $fieldName, string $value): void
    {
        $this->getHTMLPage()->find($this->parentLocator)->find($this->getLocator($fieldName))->setValue($value);
    }

    private function addClass(ElementInterface $element, string $class): void
    {
        $this->getSession()->executeScript(
            sprintf(
                "%s.classList.add('%s')",
                $this->getElementScript($element),
                $class
            )
        );
    }

    private function getElementScript(ElementInterface $element): string
    {
        return sprintf(
            'document.evaluate("%s", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue',
            $element->getXPath()
        );
    }
}
