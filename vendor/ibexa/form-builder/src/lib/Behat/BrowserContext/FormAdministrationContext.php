<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FormBuilder\Behat\BrowserContext;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Ibexa\AdminUi\Behat\Component\Dialog;
use Ibexa\FormBuilder\Behat\Component\FormSubmissionModal;
use Ibexa\FormBuilder\Behat\Component\FormSubmissionsTab;
use PHPUnit\Framework\Assert;

class FormAdministrationContext implements Context
{
    /** @var \Ibexa\FormBuilder\Behat\Component\FormSubmissionsTab */
    private $formSubmissionsTab;

    /** @var \Ibexa\FormBuilder\Behat\Component\FormSubmissionModal */
    private $formSubmissionModal;

    /** @var \Ibexa\AdminUi\Behat\Component\Dialog */
    private $dialog;

    public function __construct(FormSubmissionsTab $formSubmissionsTab, FormSubmissionModal $formSubmissionModal, Dialog $dialog)
    {
        $this->formSubmissionsTab = $formSubmissionsTab;
        $this->formSubmissionModal = $formSubmissionModal;
        $this->dialog = $dialog;
    }

    /**
     * @Then I see submissions in the form
     */
    public function iSeeFormSubmissions(TableNode $data)
    {
        $this->formSubmissionsTab->verifyIsLoaded();
        foreach ($data->getHash() as $expectedSubmission) {
            Assert::assertTrue($this->formSubmissionsTab->hasSubmission($expectedSubmission['value']));
        }
    }

    /**
     * @Then there is a total of :expectedSubmissionCount submissions
     */
    public function iCanSeeExpectedNumberOfSubmissions(string $expectedSubmissionsCount)
    {
        $this->formSubmissionsTab->verifyIsLoaded();
        Assert::assertEquals((int)$expectedSubmissionsCount, $this->formSubmissionsTab->getSubmissionsCount());
    }

    /**
     * @Then the submissions don't exist
     */
    public function iCantSeeFormSubmissions(TableNode $data)
    {
        $this->formSubmissionsTab->verifyIsLoaded();
        foreach ($data->getHash() as $missingSubmission) {
            Assert::assertFalse($this->formSubmissionsTab->hasSubmission($missingSubmission['value']));
        }
    }

    /**
     * @When I view the :submissionName submission
     */
    public function functioniClickToShowSubmission(string $submissionName)
    {
        $this->formSubmissionsTab->view($submissionName);
    }

    /**
     * @Then I see modal with correct submissions data
     */
    public function iSeeModalWithCorrectData(TableNode $data)
    {
        $this->formSubmissionModal->verifyIsLoaded();
        foreach ($data->getHash() as $row) {
            $this->formSubmissionModal->verifyFieldValue($row['label'], $row['value']);
        }
    }

    /**
     * @When I delete the :submissionName submission
     */
    public function iDeleteSubmission(string $submissionName)
    {
        $this->formSubmissionsTab->delete($submissionName);
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    /**
     * @Then delete submissions button is disabled
     */
    public function deleteSubmissionsButtonIsDisabled()
    {
        Assert::assertFalse(
            $this->formSubmissionsTab->isDeleteButtonEnabled(),
            'Delete button is enabled.'
        );
    }
}
