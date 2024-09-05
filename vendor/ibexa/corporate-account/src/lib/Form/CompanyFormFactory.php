<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form;

use Ibexa\ContentForms\Data\Mapper\ContentCreateMapper;
use Ibexa\ContentForms\Data\Mapper\ContentUpdateMapper;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Ibexa\CorporateAccount\Form\Type\Company\CompanyBasicInformationEditType;
use Ibexa\CorporateAccount\Form\Type\Company\CompanyBillingAddressEditType;
use Ibexa\CorporateAccount\Form\Type\Company\CompanyBulkDeactivateType;
use Ibexa\CorporateAccount\Form\Type\Company\CompanyContactEditType;
use Ibexa\CorporateAccount\Form\Type\Company\CompanyCreateType;
use Ibexa\CorporateAccount\Form\Type\Company\CompanyEditType;
use Ibexa\CorporateAccount\Form\Type\Company\CompanySearchType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class CompanyFormFactory extends ContentFormFactory
{
    private UrlGeneratorInterface $urlGenerator;

    private ContentTypeService $contentTypeService;

    private LocationService $locationService;

    public function __construct(
        FormFactoryInterface $formFactory,
        CorporateAccountConfiguration $corporateAccount,
        UrlGeneratorInterface $urlGenerator,
        ContentTypeService $contentTypeService,
        LocationService $locationService
    ) {
        parent::__construct($formFactory, $corporateAccount);

        $this->urlGenerator = $urlGenerator;
        $this->contentTypeService = $contentTypeService;
        $this->locationService = $locationService;
    }

    public function getSearchForm(): FormInterface
    {
        return $this->formFactory->create(CompanySearchType::class, null, [
            'method' => Request::METHOD_GET,
            'csrf_protection' => false,
        ]);
    }

    public function getBulkDeactivateForm(): FormInterface
    {
        return $this->formFactory->create(CompanyBulkDeactivateType::class, null, [
            'method' => Request::METHOD_POST,
            'action' => $this->urlGenerator->generate('ibexa.corporate_account.company.bulk_deactivate'),
        ]);
    }

    public function getBasicInformationEditForm(Company $company): FormInterface
    {
        $companyContent = $company->getContent();
        $mainLanguageCode = $companyContent->getVersionInfo()->getContentInfo()->mainLanguageCode;

        $companyUpdateData = (new ContentUpdateMapper())->mapToFormData($company->getContent(), [
            'languageCode' => $mainLanguageCode,
            'contentType' => $companyContent->getContentType(),
        ]);

        return $this->createContentEditForm(CompanyBasicInformationEditType::class, $companyUpdateData, [
            'languageCode' => $mainLanguageCode,
            'mainLanguageCode' => $mainLanguageCode,
            'drafts_enabled' => false,
        ]);
    }

    public function getEditForm(Company $company): FormInterface
    {
        $companyContent = $company->getContent();
        $mainLanguageCode = $companyContent->getVersionInfo()->getContentInfo()->mainLanguageCode;

        $companyUpdateData = (new ContentUpdateMapper())->mapToFormData($company->getContent(), [
            'languageCode' => $mainLanguageCode,
            'contentType' => $companyContent->getContentType(),
        ]);

        return $this->createContentEditForm(CompanyEditType::class, $companyUpdateData, [
            'languageCode' => $mainLanguageCode,
            'mainLanguageCode' => $mainLanguageCode,
            'drafts_enabled' => false,
        ]);
    }

    public function getBillingAddressEditForm(Company $company): FormInterface
    {
        $companyContent = $company->getContent();
        $mainLanguageCode = $companyContent->getVersionInfo()->getContentInfo()->mainLanguageCode;

        $companyUpdateData = (new ContentUpdateMapper())->mapToFormData($company->getContent(), [
            'languageCode' => $mainLanguageCode,
            'contentType' => $companyContent->getContentType(),
        ]);

        return $this->createContentEditForm(CompanyBillingAddressEditType::class, $companyUpdateData, [
            'languageCode' => $mainLanguageCode,
            'mainLanguageCode' => $mainLanguageCode,
            'drafts_enabled' => false,
        ]);
    }

    public function getContactEditForm(Company $company): FormInterface
    {
        $companyContent = $company->getContent();
        $mainLanguageCode = $companyContent->getVersionInfo()->getContentInfo()->mainLanguageCode;

        $companyUpdateData = (new ContentUpdateMapper())->mapToFormData($company->getContent(), [
            'languageCode' => $mainLanguageCode,
            'contentType' => $companyContent->getContentType(),
        ]);

        return $this->createContentEditForm(CompanyContactEditType::class, $companyUpdateData, [
            'languageCode' => $mainLanguageCode,
            'mainLanguageCode' => $mainLanguageCode,
            'drafts_enabled' => false,
        ]);
    }

    public function getCreateForm(Location $parentLocation, string $languageCode): FormInterface
    {
        $contentType = $this->contentTypeService->loadContentTypeByIdentifier(
            $this->corporateAccount->getCompanyContentTypeIdentifier()
        );

        $companyCreateData = (new ContentCreateMapper())->mapToFormData($contentType, [
            'mainLanguageCode' => $languageCode,
            'parentLocation' => $this->locationService->newLocationCreateStruct($parentLocation->id),
        ]);

        return $this->createContentEditForm(CompanyCreateType::class, $companyCreateData, [
            'languageCode' => $languageCode,
            'mainLanguageCode' => $languageCode,
            'drafts_enabled' => false,
        ]);
    }
}
