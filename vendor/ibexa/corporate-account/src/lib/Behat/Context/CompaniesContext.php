<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Ibexa\CorporateAccount\Behat\Page\CompaniesPage;
use Ibexa\CorporateAccount\Behat\Page\CompanyPage;
use PHPUnit\Framework\Assert;

final class CompaniesContext implements Context
{
    private CompaniesPage $companiesPage;

    private CompanyPage $companyPage;

    public function __construct(CompaniesPage $companiesPage, CompanyPage $companyPage)
    {
        $this->companiesPage = $companiesPage;
        $this->companyPage = $companyPage;
    }

    /**
     * @Then Company :companyName exists and has :companyStatus status on Companies page
     */
    public function iConfirmThatCompanyHasStatus(string $companyName, string $companyStatus): void
    {
        $this->companiesPage->verifyIsLoaded();
        Assert::assertTrue($this->companiesPage->companyWithStatusExists($companyName, $companyStatus));
    }

    /**
     * @Then Company status is :companyStatus on Company page
     */
    public function iVerifyCompanyStatusOnCompanyPage(string $companyName): void
    {
        $this->companyPage->verifyCompanyStatus($companyName);
    }

    /**
     * @When I edit :companyName Company
     */
    public function iEditCompany(string $companyName): void
    {
        $this->companiesPage->editCompany($companyName);
    }

    /**
     * @When I view :companyName Company
     */
    public function iViewCompany(string $companyName): void
    {
        $this->companiesPage->viewCompany($companyName);
    }

    /**
     * @When I deactivate :companyName Company
     */
    public function iDeactivateCompany(string $companyName): void
    {
        $this->companiesPage->deactivateCompany($companyName);
    }

    /**
     * @When I activate :companyName Company
     */
    public function iActivateCompany(string $companyName): void
    {
        $this->companiesPage->activateCompany($companyName);
    }

    /**
     * @Then I should see a Company summary with values
     */
    public function iVerifyCompanySummary(TableNode $table): void
    {
        $this->companyPage->setExpectedCompanyName($table->getHash()[0]['Company name']);
        $this->companyPage->verifyIsLoaded();
        foreach ($table->getHash() as $row) {
            $this->companyPage->verifyCompanySummary($row['Company name'], $row['Location'], $row['Sales Representative'], $row['Tax ID'], $row['Website']);
        }
    }

    /**
     * @Then I should see a Company profile with values
     */
    public function iVerifyCompanyProfile(TableNode $table): void
    {
        $this->companyPage->setExpectedCompanyName($table->getHash()[0]['Company name']);
        $this->companyPage->verifyIsLoaded();
        foreach ($table->getHash() as $row) {
            $this->companyPage->verifyCompanyProfile($row['Company name'], $row['Tax ID'], $row['Website'], $row['Customer Group'], $row['Sales Representative'], $row['Name'], $row['Email'], $row['Phone'], $row['Address']);
        }
    }

    /**
     * @Then I open :companyName Company page in admin SiteAccess
     */
    public function iOpenCompanyPage(string $companyName): void
    {
        $this->companyPage->setExpectedCompanyName($companyName);
        $this->companyPage->open('admin');
        $this->companyPage->verifyIsLoaded();
    }
}
