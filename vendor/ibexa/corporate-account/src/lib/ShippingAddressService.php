<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Subtree;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\CorporateAccount\Service\ShippingAddressService as ShippingAddressServiceInterface;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Contracts\CorporateAccount\Values\ShippingAddress;
use Ibexa\Contracts\CorporateAccount\Values\ShippingAddressCreateStruct;
use Ibexa\Contracts\CorporateAccount\Values\ShippingAddressUpdateStruct;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Ibexa\CorporateAccount\Values\Mapper\DomainMapperInterface;

final class ShippingAddressService implements ShippingAddressServiceInterface
{
    private CorporateAccountConfiguration $corporateAccountConfiguration;

    private DomainMapperInterface $domainMapper;

    private SearchService $searchService;

    private ContentService $contentService;

    private LocationService $locationService;

    private ContentTypeService $contentTypeService;

    private ConfigResolverInterface $configResolver;

    public function __construct(
        CorporateAccountConfiguration $corporateAccountConfiguration,
        DomainMapperInterface $domainMapper,
        SearchService $searchService,
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        LocationService $locationService,
        ConfigResolverInterface $configResolver
    ) {
        $this->corporateAccountConfiguration = $corporateAccountConfiguration;
        $this->domainMapper = $domainMapper;
        $this->searchService = $searchService;
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->locationService = $locationService;
        $this->configResolver = $configResolver;
    }

    public function getShippingAddress(int $id): ShippingAddress
    {
        return $this->domainMapper->mapShippingAddress(
            $this->contentService->loadContent($id)
        );
    }

    public function getCompanyDefaultShippingAddress(
        Company $company
    ): ?ShippingAddress {
        if ($company->getDefaultAddressId() === null) {
            return null;
        }

        try {
            return $this->domainMapper->mapShippingAddress(
                $this->contentService->loadContent(
                    $company->getDefaultAddressId()
                )
            );
        } catch (NotFoundException $exception) {
            return null;
        }
    }

    /** @return \Ibexa\Contracts\CorporateAccount\Values\ShippingAddress[]> */
    public function getCompanyShippingAddresses(
        Company $company,
        int $limit = 25,
        int $offset = 0
    ): array {
        $query = new Query([
            'filter' => new LogicalAnd(
                [
                    new Subtree(
                        $company->getLocationPath()
                    ),
                    new ContentTypeIdentifier($this->corporateAccountConfiguration->getShippingAddressContentTypeIdentifier()),
                ]
            ),
            'offset' => $offset,
            'limit' => $limit,
        ]);

        $result = $this->searchService->findContent(
            $query
        );

        return array_map(function (SearchHit $searchHit): ShippingAddress {
            /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $address */
            $address = $searchHit->valueObject;

            return $this->domainMapper->mapShippingAddress($address);
        }, $result->searchHits);
    }

    public function createShippingAddress(
        Company $company,
        ShippingAddressCreateStruct $shippingAddressCreateStruct,
        ?array $fieldIdentifiersToValidate = null
    ): ShippingAddress {
        $addressBookId = $company->getAddressBookId();
        if ($addressBookId === null) {
            throw new \LogicException('Creating of shipping address is not possible without address book');
        }

        $addressBook = $this->contentService->loadContent($company->getAddressBookId());

        $draft = $this->contentService->createContent(
            $shippingAddressCreateStruct,
            [
                $this->locationService->newLocationCreateStruct(
                    $addressBook->contentInfo->mainLocationId
                ),
            ],
            $fieldIdentifiersToValidate
        );

        return $this->domainMapper->mapShippingAddress(
            $this->contentService->publishVersion($draft->versionInfo)
        );
    }

    public function updateShippingAddress(
        ShippingAddress $shippingAddress,
        ShippingAddressUpdateStruct $shippingAddressUpdateStruct,
        ?array $fieldIdentifiersToValidate = null
    ): ShippingAddress {
        $content = $shippingAddress->getContent();

        $draft = $this->contentService->createContentDraft($content->contentInfo);
        $updatedDraft = $this->contentService->updateContent(
            $draft->versionInfo,
            $shippingAddressUpdateStruct,
            $fieldIdentifiersToValidate
        );

        return $this->domainMapper->mapShippingAddress(
            $this->contentService->publishVersion($updatedDraft->versionInfo)
        );
    }

    public function deleteShippingAddress(ShippingAddress $shippingAddress): void
    {
        $this->contentService->deleteContent($shippingAddress->getContent()->contentInfo);
    }

    public function createShippingAddressFromCompanyBillingAddress(Company $company): ShippingAddress
    {
        $billingAddress = $company->getBillingAddress();

        $shippingAddressCreateStruct = $this->newShippingAddressCreateStruct();
        $shippingAddressCreateStruct->setField('address', $billingAddress);
        $shippingAddressCreateStruct->setField('name', $billingAddress->name);

        return $this->createShippingAddress($company, $shippingAddressCreateStruct);
    }

    public function newShippingAddressCreateStruct(): ShippingAddressCreateStruct
    {
        $contentType = $this->contentTypeService->loadContentTypeByIdentifier('shipping_address');
        $mainLanguageCode = $this->getDefaultLanguageCode();

        return new ShippingAddressCreateStruct([
            'contentType' => $contentType,
            'mainLanguageCode' => $mainLanguageCode,
            'alwaysAvailable' => $contentType->defaultAlwaysAvailable,
        ]);
    }

    public function newShippingAddressUpdateStruct(): ShippingAddressUpdateStruct
    {
        return new ShippingAddressUpdateStruct();
    }

    private function getDefaultLanguageCode(): string
    {
        $languages = $this->configResolver->getParameter('languages');

        return reset($languages);
    }
}
