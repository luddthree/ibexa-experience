<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FormBuilder\Behat\BrowserContext;

use Behat\Behat\Context\Context;
use Ibexa\FormBuilder\Behat\Component\FormBuilderEditor;
use Ibexa\FormBuilder\Behat\Component\FormFields\FormFieldRegistry;

class FormFieldConfigurationContext implements Context
{
    /** @var \Ibexa\FormBuilder\Behat\Component\FormFields\FormFieldRegistry */
    private $formFieldRegistry;

    /** @var \Ibexa\FormBuilder\Behat\Component\FormBuilderEditor */
    private $formBuilderPage;

    public function __construct(FormFieldRegistry $formFieldRegistry, FormBuilderEditor $formBuilderPage)
    {
        $this->formFieldRegistry = $formFieldRegistry;
        $this->formBuilderPage = $formBuilderPage;
    }

    /**
     * @When I start configuring the :fieldName field
     */
    public function iConfigureFieldFromForm(string $fieldName)
    {
        $this->formBuilderPage->verifyIsLoaded();
        $this->formBuilderPage->openFieldSettings($fieldName);
    }

    /**
     * @When I set custom configuration for the :fieldName field
     */
    public function iSetCustomConfigurationForField(string $fieldType)
    {
        $field = $this->formFieldRegistry->getField($fieldType);
        $field->verifyIsLoaded();
        $field->setDefaultTestingConfiguration();
    }

    /**
     * @When I confirm the :fieldType field configuration
     */
    public function iConfirmFieldConfiguration(string $fieldType)
    {
        $this->formFieldRegistry->getField($fieldType)->submitForm();
    }

    /**
     * @Then I should see test configuration set on :fieldType fields
     */
    public function iShoudSeeFormWithConfiguredFields(string $fieldType)
    {
        $this->formFieldRegistry->getField($fieldType)->verifyDefaultTestingConfiguration();
    }
}
