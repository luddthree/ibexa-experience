<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\CorporateAccount\Service\CompanyService as CompanyServiceInterface;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Contracts\CorporateAccount\Values\CompanyCreateStruct;
use Ibexa\Contracts\CorporateAccount\Values\CompanyUpdateStruct;
use Ibexa\Contracts\CorporateAccount\Values\Member;
use Ibexa\Contracts\CorporateAccount\Values\ShippingAddress;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Core\FieldType\Relation\Value as RelationValue;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Ibexa\CorporateAccount\Values\Mapper\DomainMapperInterface;

final class CompanyService implements CompanyServiceInterface
{
    private const ADDRESS_BOOK_NAME = 'Address Book';

    private const MEMBERS_NAME = 'Members';

    private CorporateAccountConfiguration $corporateAccountConfiguration;

    private DomainMapperInterface $domainMapper;

    private ContentService $contentService;

    private ContentTypeService $contentTypeService;

    private LocationService $locationService;

    private UserService $userService;

    private ConfigResolverInterface $configResolver;

    private SearchService $searchService;

    public function __construct(
        CorporateAccountConfiguration $corporateAccountConfiguration,
        DomainMapperInterface $domainMapper,
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        LocationService $locationService,
        UserService $userService,
        ConfigResolverInterface $configResolver,
        SearchService $searchService
    ) {
        $this->corporateAccountConfiguration = $corporateAccountConfiguration;
        $this->domainMapper = $domainMapper;
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->locationService = $locationService;
        $this->userService = $userService;
        $this->configResolver = $configResolver;
        $this->searchService = $searchService;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function getCompany(int $id): Company
    {
        $content = $this->contentService->loadContent($id);
        if (!$this->isCompany($content)) {
            throw new NotFoundException('Company', $id);
        }

        return $this->domainMapper->mapCompany($content);
    }

    public function getCompaniesCount(
        ?Criterion $filter = null
    ): int {
        $result = $this->getCompanyContentSearchResults($filter, [], 0);

        return (int)$result->totalCount;
    }

    public function getCompanies(
        ?Criterion $filter = null,
        array $sortClauses = [],
        ?int $limit = 25,
        int $offset = 0
    ): array {
        $result = $this->getCompanyContentSearchResults($filter, $sortClauses, $limit, $offset);

        return array_map(function (SearchHit $searchHit): Company {
            /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $companyContent */
            $companyContent = $searchHit->valueObject;

            return $this->domainMapper->mapCompany($companyContent);
        }, $result->searchHits);
    }

    public function getCompanySalesRepresentative(Company $company): ?User
    {
        return $company->getSalesRepresentativeId()
            ? $this->userService->loadUser($company->getSalesRepresentativeId())
            : null;
    }

    public function setDefaultShippingAddress(
        Company $company,
        ShippingAddress $shippingAddress
    ): void {
        $companyUpdateStruct = $this->newCompanyUpdateStruct();
        $companyUpdateStruct->setField('default_address', new RelationValue($shippingAddress->getId()));

        $this->updateCompany($company, $companyUpdateStruct);
    }

    public function setContact(
        Company $company,
        Member $contact
    ): void {
        $companyUpdateStruct = $this->newCompanyUpdateStruct();
        $companyUpdateStruct->setField('contact', new RelationValue($contact->getId()));

        $this->updateCompany($company, $companyUpdateStruct);
    }

    public function setCompanyAddressBookRelation(
        Company $company,
        Content $content
    ): void {
        $companyUpdateStruct = $this->newCompanyUpdateStruct();
        $companyUpdateStruct->setField('address_book', new RelationValue($content->id));

        $this->updateCompany($company, $companyUpdateStruct);
    }

    public function setCompanyMembersRelation(
        Company $company,
        Content $content
    ): void {
        $companyUpdateStruct = $this->newCompanyUpdateStruct();
        $companyUpdateStruct->setField('members', new RelationValue($content->id));

        $this->updateCompany($company, $companyUpdateStruct);
    }

    public function createCompanyAddressBookFolder(Company $company): Content
    {
        $languages = $this->configResolver->getParameter('languages');
        $defaultLanguageCode = reset($languages);

        $folderContentType = $this->contentTypeService->loadContentTypeByIdentifier('folder');
        $contentCreateStruct = $this->contentService->newContentCreateStruct(
            $folderContentType,
            $defaultLanguageCode
        );

        $contentCreateStruct->setField('name', self::ADDRESS_BOOK_NAME);

        $draft = $this->contentService->createContent(
            $contentCreateStruct,
            [
                $this->locationService->newLocationCreateStruct(
                    $company->getContent()->contentInfo->mainLocationId
                ),
            ]
        );

        $addressBook = $this->contentService->publishVersion($draft->versionInfo);

        if ($company->getAddressBookId() === null) {
            $this->setCompanyAddressBookRelation($company, $addressBook);
        }

        return $addressBook;
    }

    public function createCompanyMembersUserGroup(Company $company): Content
    {
        $languages = $this->configResolver->getParameter('languages');
        $defaultLanguageCode = reset($languages);

        $folderContentType = $this->contentTypeService->loadContentTypeByIdentifier('user_group');
        $contentCreateStruct = $this->contentService->newContentCreateStruct(
            $folderContentType,
            $defaultLanguageCode
        );

        $contentCreateStruct->setField('name', self::MEMBERS_NAME);

        $draft = $this->contentService->createContent(
            $contentCreateStruct,
            [
                $this->locationService->newLocationCreateStruct(
                    $company->getContent()->contentInfo->mainLocationId
                ),
            ]
        );

        $members = $this->contentService->publishVersion($draft->versionInfo);

        if ($company->getMembersId() === null) {
            $this->setCompanyMembersRelation($company, $members);
        }

        return $members;
    }

    public function createCompany(
        CompanyCreateStruct $companyCreateStruct,
        ?array $fieldIdentifiersToValidate = null
    ): Company {
        $location = $this->locationService->loadLocationByRemoteId(
            $this->corporateAccountConfiguration->getParentLocationRemoteId()
        );

        $draft = $this->contentService->createContent(
            $companyCreateStruct,
            [
                $this->locationService->newLocationCreateStruct($location->id),
            ],
            $fieldIdentifiersToValidate
        );

        return $this->domainMapper->mapCompany(
            $this->contentService->publishVersion($draft->versionInfo)
        );
    }

    public function updateCompany(
        Company $company,
        CompanyUpdateStruct $companyUpdateStruct,
        ?array $fieldIdentifiersToValidate = null
    ): Company {
        $content = $company->getContent();

        $draft = $this->contentService->createContentDraft($content->contentInfo);
        $updatedDraft = $this->contentService->updateContent(
            $draft->versionInfo,
            $companyUpdateStruct,
            $fieldIdentifiersToValidate
        );

        return $this->domainMapper->mapCompany(
            $this->contentService->publishVersion($updatedDraft->versionInfo)
        );
    }

    public function deleteCompany(Company $company): void
    {
        $this->contentService->deleteContent($company->getContent()->contentInfo);
    }

    public function newCompanyCreateStruct(): CompanyCreateStruct
    {
        $contentType = $this->contentTypeService->loadContentTypeByIdentifier(
            $this->corporateAccountConfiguration->getCompanyContentTypeIdentifier()
        );
        $mainLanguageCode = $this->getDefaultLanguageCode();

        return new CompanyCreateStruct([
            'contentType' => $contentType,
            'mainLanguageCode' => $mainLanguageCode,
            'alwaysAvailable' => $contentType->defaultAlwaysAvailable,
        ]);
    }

    public function newCompanyUpdateStruct(): CompanyUpdateStruct
    {
        return new CompanyUpdateStruct();
    }

    private function getDefaultLanguageCode(): string
    {
        $languages = $this->configResolver->getParameter('languages');

        return reset($languages);
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause[] $sortClauses
     */
    private function getCompanyContentSearchResults(
        ?Criterion $filter,
        array $sortClauses = [],
        ?int $limit = 25,
        int $offset = 0
    ): SearchResult {
        $companyCriterion = new ContentTypeIdentifier(
            $this->corporateAccountConfiguration->getCompanyContentTypeIdentifier()
        );

        $filter = new LogicalAnd(
            $filter !== null
                ? [$companyCriterion, $companyCriterion, $filter]
                : [$companyCriterion, $companyCriterion]
        );

        $query = new Query([
            'filter' => $filter,
            'offset' => $offset,
            'limit' => $limit,
            'sortClauses' => $sortClauses,
        ]);

        return $this->searchService->findContent($query);
    }

    private function isCompany(Content $content): bool
    {
        return $content->getContentType()->identifier ===
            $this->corporateAccountConfiguration->getCompanyContentTypeIdentifier();
    }
}
