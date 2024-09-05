<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Behat\Page;

use Behat\Mink\Session;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Routing\Router;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\CorporateAccount\Values\Query\Criterion\CompanyName;
use Ibexa\CorporateAccount\CompanyService;

final class CompanyPage extends Page
{
    private string $expectedCompanyName;

    private int $expectedCompanyId;

    private CompanyService $companyService;

    public function __construct(Session $session, Router $router, CompanyService $companyService)
    {
        parent::__construct($session, $router);
        $this->companyService = $companyService;
    }

    public function setExpectedCompanyName(string $companyName): void
    {
        $this->expectedCompanyName = $companyName;
        $query = new CompanyName(Operator::EQ, $this->expectedCompanyName);
        $company = $this->companyService->getCompanies($query)[0];
        $this->expectedCompanyId = $company->getId();
    }

    public function verifyCompanyStatus(string $companyStatus): void
    {
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('companyStatus'))->assert()->textEquals($companyStatus);
    }

    public function verifyCompanySummary(string $companyName, string $companyLocation, string $companySalesRep, string $companyTaxId, string $companyWebsite): void
    {
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('companyPageHeader'))->assert()->textContains($companyName);
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('companySummaryLocation'))->assert()->textEquals($companyLocation);
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('companySummarySalesRep'))->assert()->textEquals($companySalesRep);
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('companySummaryTaxId'))->assert()->textEquals($companyTaxId);
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('companySummaryWebsite'))->assert()->textEquals($companyWebsite);
    }

    public function verifyCompanyProfile(string $companyName, string $companyTaxId, string $companyWebsite, string $companyCustomerGroup, string $companySalesRep, string $billingAddressCompanyName, string $billingAddressEmail, string $billingAddressPhone, string $billingAddress): void
    {
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('companyProfileLocation'))->assert()->textEquals($companyName);
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('companyProfileTaxId'))->assert()->textEquals($companyTaxId);
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('companyProfileWebsite'))->assert()->textEquals($companyWebsite);
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('companyProfileCustomerGroup'))->assert()->textEquals($companyCustomerGroup);
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('companyProfileSalesRep'))->assert()->textEquals($companySalesRep);
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('companyProfileBillingAddressName'))->assert()->textEquals($billingAddressCompanyName);
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('companyProfileBillingAddressEmail'))->assert()->textEquals($billingAddressEmail);
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('companyProfileBillingAddressPhone'))->assert()->textEquals($billingAddressPhone);
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('companyProfileBillingAddress'))->assert()->textEquals($billingAddress);
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('companyPageHeader'))->assert()->textContains($this->expectedCompanyName);
    }

    public function getName(): string
    {
        return 'Company';
    }

    protected function getRoute(): string
    {
        return sprintf(
            '/company/details/%d',
            $this->expectedCompanyId
        );
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('companyPageHeader', '.ibexa-page-title__title'),
            new VisibleCSSLocator('companyStatus', '.ibexa-badge'),
            new VisibleCSSLocator('companySummaryLocation', 'div.ibexa-details__item:nth-of-type(1) .ibexa-details__item-content'),
            new VisibleCSSLocator('companySummarySalesRep', 'div.ibexa-details__item:nth-of-type(2) .ibexa-details__item-content'),
            new VisibleCSSLocator('companySummaryTaxId', 'div.ibexa-details__item:nth-of-type(3) .ibexa-details__item-content'),
            new VisibleCSSLocator('companySummaryWebsite', 'div.ibexa-details__item:nth-of-type(4) .ibexa-details__item-content'),
            new VisibleCSSLocator('companyProfileLocation', 'div.ibexa-ca-company-tab-company-profile__top-wrapper .ibexa-details__items div.ibexa-details__item:nth-of-type(1) .ibexa-details__item-content'),
            new VisibleCSSLocator('companyProfileTaxId', 'div.ibexa-ca-company-tab-company-profile__top-wrapper .ibexa-details__items div.ibexa-details__item:nth-of-type(2) .ibexa-details__item-content'),
            new VisibleCSSLocator('companyProfileWebsite', 'div.ibexa-ca-company-tab-company-profile__top-wrapper .ibexa-details__items div.ibexa-details__item:nth-of-type(3) .ibexa-details__item-content'),
            new VisibleCSSLocator('companyProfileCustomerGroup', 'div.ibexa-ca-company-tab-company-profile__top-wrapper .ibexa-details__items div.ibexa-details__item:nth-of-type(4) .ibexa-details__item-content'),
            new VisibleCSSLocator('companyProfileSalesRep', 'div.ibexa-ca-company-tab-company-profile__top-wrapper .ibexa-details__items div.ibexa-details__item:nth-of-type(5) .ibexa-details__item-content'),
            new VisibleCSSLocator('companyProfileBillingAddressName', 'div.ibexa-ca-company-tab-company-profile__billing-address-wrapper .ibexa-details__items div.ibexa-details__item:nth-of-type(1) .ibexa-details__item-content'),
            new VisibleCSSLocator('companyProfileBillingAddressEmail', 'div.ibexa-ca-company-tab-company-profile__billing-address-wrapper .ibexa-details__items div.ibexa-details__item:nth-of-type(2) .ibexa-details__item-content'),
            new VisibleCSSLocator('companyProfileBillingAddressPhone', 'div.ibexa-ca-company-tab-company-profile__billing-address-wrapper .ibexa-details__items div.ibexa-details__item:nth-of-type(3) .ibexa-details__item-content'),
            new VisibleCSSLocator('companyProfileBillingAddress', 'div.ibexa-ca-company-tab-company-profile__billing-address-wrapper .ibexa-details__items div.ibexa-details__item:nth-of-type(4) .ibexa-details__item-content'),
        ];
    }
}
