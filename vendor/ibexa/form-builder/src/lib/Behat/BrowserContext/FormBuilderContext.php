<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FormBuilder\Behat\BrowserContext;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Ibexa\AdminUi\Behat\Page\ContentUpdateItemPage;
use Ibexa\FormBuilder\Behat\Component\FormBuilderEditor;
use Ibexa\FormBuilder\Behat\Component\FormPreviewPanel;
use PHPUnit\Framework\Assert;

class FormBuilderContext implements Context
{
    /** @var \Ibexa\FormBuilder\Behat\Component\FormBuilderEditor */
    private $formBuilderPage;

    /** @var \Ibexa\AdminUi\Behat\Page\ContentUpdateItemPage */
    private $contentUpdateItemPage;

    /** @var \Ibexa\FormBuilder\Behat\Component\FormPreviewPanel */
    private $formPreviewPanel;

    public function __construct(
        FormBuilderEditor $formBuilderPage,
        ContentUpdateItemPage $contentUpdateItemPage,
        FormPreviewPanel $formPreviewPanel
    ) {
        $this->formBuilderPage = $formBuilderPage;
        $this->contentUpdateItemPage = $contentUpdateItemPage;
        $this->formPreviewPanel = $formPreviewPanel;
    }

    /**
     * @When I go back from :formName form builder to content edit
     */
    public function iGoBackToContentEdit(string $formName)
    {
        $this->formBuilderPage->verifyIsLoaded();
        $this->formBuilderPage->goBackToContentCreation();
        $this->contentUpdateItemPage->verifyIsLoaded();
    }

    /**
     * @When I add the :fieldName field to form
     */
    public function iAddFieldToForm(string $fieldName)
    {
        $this->formBuilderPage->verifyIsLoaded();
        $this->formBuilderPage->addFieldToForm($fieldName);
    }

    /**
     * @When I add the :fieldName fields to form
     */
    public function iAddFieldsToForm(TableNode $fields)
    {
        $this->formBuilderPage->verifyIsLoaded();

        foreach ($fields->getHash() as $row) {
            $this->formBuilderPage->addFieldToForm($row['fieldName']);
        }
    }

    /**
     * @When I delete the :fieldName field from form
     */
    public function iDeleteFieldFromForm(string $fieldName)
    {
        $this->formBuilderPage->verifyIsLoaded();
        $this->formBuilderPage->removeField($fieldName);
    }

    /**
     * @When I delete the :fieldName fields from form
     */
    public function iDeleteFieldsFromForm(TableNode $fields)
    {
        $this->formBuilderPage->verifyIsLoaded();

        foreach ($fields->getHash() as $row) {
            $this->formBuilderPage->removeField($row['fieldName']);
        }
    }

    /**
     * @When I start creating a new Form
     */
    public function iStartCreateForm()
    {
        $this->formPreviewPanel->createForm();
        $this->formBuilderPage->verifyIsLoaded();
    }

    /**
     * @When I start editing the form
     */
    public function iStartEditingForm()
    {
        $this->contentUpdateItemPage->verifyIsLoaded();
        $this->formPreviewPanel->editForm();
        $this->formBuilderPage->verifyIsLoaded();
    }

    /**
     * @When I delete the form from Form Content Item
     */
    public function iDeleteFormFromFormContentItem()
    {
        $this->contentUpdateItemPage->verifyIsLoaded();
        $this->formPreviewPanel->deleteForm();
    }

    /**
     * @Then I should see empty form preview
     */
    public function iShouldSeeEmptyFormPreview()
    {
        $this->formPreviewPanel->verifyIsEmpty();
    }

    /**
     * @Then form preview contains a hidden field
     */
    public function previewContainsAHiddenField()
    {
        Assert::assertTrue($this->formPreviewPanel->hasHiddenField(), 'Hidden field not found.');
    }

    /**
     * @Then I should see form preview with fields
     */
    public function iShouldSeeFormPreviewWithFields(TableNode $data)
    {
        foreach ($data->getHash() as $row) {
            Assert::assertTrue($this->formPreviewPanel->isFieldPresent($row['fieldName']));
        }
    }

    /**
     * @Then I should see form preview without fields
     */
    public function iShouldSeeFormPreviewWithoutFields(TableNode $data)
    {
        foreach ($data->getHash() as $row) {
            Assert::assertFalse($this->formPreviewPanel->isFieldPresent($row['fieldName']));
        }
    }
}
