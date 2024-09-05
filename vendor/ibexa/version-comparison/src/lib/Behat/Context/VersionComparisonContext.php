<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\VersionComparison\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Ibexa\Behat\Core\Behat\ArgumentParser;
use Ibexa\VersionComparison\Behat\Component\VersionTab;
use Ibexa\VersionComparison\Behat\Page\SideBySideComparisonPage;
use Ibexa\VersionComparison\Behat\Page\SingleColumnComparisonPage;

class VersionComparisonContext implements Context
{
    private SideBySideComparisonPage $sideBySideComparisonPage;

    private SingleColumnComparisonPage $singleColumnComparisonPage;

    private VersionTab $versionTab;

    private ArgumentParser $argumentParser;

    public function __construct(
        SideBySideComparisonPage $sideBySideComparisonPage,
        SingleColumnComparisonPage $singleColumnComparisonPage,
        VersionTab $versionTab,
        ArgumentParser $argumentParser
    ) {
        $this->sideBySideComparisonPage = $sideBySideComparisonPage;
        $this->singleColumnComparisonPage = $singleColumnComparisonPage;
        $this->versionTab = $versionTab;
        $this->argumentParser = $argumentParser;
    }

    /**
     * @When I enter version compare for version :versionNumber from :tableName
     */
    public function iEnterVersionCompareForVersionFrom(string $versionNumber, string $tableName): void
    {
        $this->versionTab->verifyIsLoaded();
        $this->versionTab->enterVersionComparison($tableName, $versionNumber);
    }

    /**
     * @Then I should be on Side by Side Version compare page with :leftSideVersion on the left side
     * @Then I should be on Side by Side Version compare page with :rightSideVersion on the right side and :leftSideVersion on the left side
     */
    public function iShouldBeOnVersionComparePageFor(string $leftSideVersionNumber, string $rightSideVersion = null)
    {
        $this->sideBySideComparisonPage->verifyIsLoaded();
        $this->sideBySideComparisonPage->verifyLeftSideVersion($leftSideVersionNumber);
        if ($rightSideVersion !== null) {
            $this->sideBySideComparisonPage->verifyRightSideVersion($rightSideVersion);
        }
    }

    /**
     * @Given I select :versionNumber for the left side of the comparison
     */
    public function iSelectAsTheBaseOfTheComparison(string $versionNumber)
    {
        $this->sideBySideComparisonPage->selectLeftSideVersion($versionNumber);
    }

    /**
     * @Then I should see correct data for the right side comparison
     */
    public function iShouldSeeCorrectDataForTheRightSideComparison(TableNode $table)
    {
        foreach ($table->getHash() as $rowData) {
            $this->sideBySideComparisonPage->verifyRightSideFieldData($rowData['fieldName'], $rowData['fieldTypeIdentifier'], $rowData);
        }
    }

    /**
     * @Given I should see correct for the left side comparison
     */
    public function iShouldSeeCorrectForTheLeftSideComparison(TableNode $table)
    {
        foreach ($table->getHash() as $rowData) {
            $this->sideBySideComparisonPage->verifyLeftSideFieldData($rowData['fieldName'], $rowData['fieldTypeIdentifier'], $rowData);
        }
    }

    /**
     * @When I select :versionNumber for the right side of the comparison
     */
    public function iSelectForTheRightSideOfTheComparison(string $versionNumber): void
    {
        $this->sideBySideComparisonPage->selectRightSideVersion($versionNumber);
    }

    /**
     * @Given I switch to single column view
     */
    public function iSwitchToSingleColumnView()
    {
        $this->sideBySideComparisonPage->switchToSingleColumnView();
        $this->singleColumnComparisonPage->verifyIsLoaded();
    }

    /**
     * @Given I should be on Single Column Version compare page with :leftSideVersion on the left side
     * @Given I should be on Single Column Version compare page with :rightSideVersion on the right side and :leftSideVersion on the left side
     */
    public function iShouldBeOnSingleColumnVersionComparePageWithOnTheLeftSide(string $leftSideVersion, string $rightSideVersion = null)
    {
        $this->singleColumnComparisonPage->verifyIsLoaded();
        $this->singleColumnComparisonPage->verifyLeftSideVersion($leftSideVersion);
        if ($rightSideVersion !== null) {
            $this->singleColumnComparisonPage->verifyRightSideVersion($rightSideVersion);
        }
    }

    /**
     * @Then I should see correct data added
     */
    public function iShouldSeeCorrectDataAdded(TableNode $table)
    {
        foreach ($table->getHash() as $rowData) {
            $this->singleColumnComparisonPage->verifyDataAdded($rowData['fieldName'], $rowData['fieldTypeIdentifier'], $rowData['valueAdded']);
        }
    }

    /**
     * @Given I should see correct data removed
     */
    public function iShouldSeeCorrectDataRemoved(TableNode $table): void
    {
        foreach ($table->getHash() as $rowData) {
            $this->singleColumnComparisonPage->verifyDataRemoved($rowData['fieldName'], $rowData['fieldTypeIdentifier'], $rowData['valueRemoved']);
        }
    }

    /**
     * @When I'm on Side by Side comparison page for :locationPath between versions :leftVersionNumber and :rightVersionNumber
     */
    public function iMOnSideBySideComparisonPageForBetweenVersionsAnd(string $locationPath, string $leftVersionNumber, string $rightVersionNumber): void
    {
        $this->sideBySideComparisonPage->setExpectedVersionComparisonData($this->argumentParser->parseUrl($locationPath), $leftVersionNumber, $rightVersionNumber);
        $this->sideBySideComparisonPage->open('admin');
    }

    /**
     * @When I'm on single column comparison page for :locationPath between versions :leftVersionNumber and :rightVersionNumber
     */
    public function iMOnSingleColumnComparisonPageForBetweenVersionsAnd(string $locationPath, string $leftVersionNumber, string $rightVersionNumber): void
    {
        $this->singleColumnComparisonPage->setExpectedVersionComparisonData($this->argumentParser->parseUrl($locationPath), $leftVersionNumber, $rightVersionNumber);
        $this->singleColumnComparisonPage->open('admin');
    }
}
