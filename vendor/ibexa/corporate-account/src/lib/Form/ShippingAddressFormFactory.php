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
use Ibexa\Contracts\CorporateAccount\Values\ShippingAddress;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Ibexa\CorporateAccount\Form\Data\Company\CompanyUpdateDefaultShippingAddressData;
use Ibexa\CorporateAccount\Form\Data\ShippingAddress\ShippingAddressItemDeleteData;
use Ibexa\CorporateAccount\Form\Type\Company\CompanyUpdateDefaultShippingAddressType;
use Ibexa\CorporateAccount\Form\Type\ShippingAddress\ShippingAddressCreateType;
use Ibexa\CorporateAccount\Form\Type\ShippingAddress\ShippingAddressEditType;
use Ibexa\CorporateAccount\Form\Type\ShippingAddress\ShippingAddressItemDeleteType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class ShippingAddressFormFactory extends ContentFormFactory
{
    private LocationService $locationService;

    private ContentTypeService $contentTypeService;

    public function __construct(
        FormFactoryInterface $formFactory,
        CorporateAccountConfiguration $corporateAccount,
        LocationService $locationService,
        ContentTypeService $contentTypeService
    ) {
        parent::__construct($formFactory, $corporateAccount);

        $this->locationService = $locationService;
        $this->contentTypeService = $contentTypeService;
    }

    public function getEditForm(ShippingAddress $shippingAddress): FormInterface
    {
        $contentInfo = $shippingAddress->getContent()->contentInfo;

        $shippingAddressUpdateData = (new ContentUpdateMapper())->mapToFormData($shippingAddress->getContent(), [
            'languageCode' => $contentInfo->mainLanguageCode,
            'contentType' => $shippingAddress->getContent()->getContentType(),
        ]);

        return $this->formFactory->create(ShippingAddressEditType::class, $shippingAddressUpdateData, [
            'languageCode' => $contentInfo->mainLanguageCode,
            'mainLanguageCode' => $contentInfo->mainLanguageCode,
            'drafts_enabled' => false,
        ]);
    }

    public function getCreateForm(Location $parentLocation, string $languageCode): FormInterface
    {
        $contentType = $this->contentTypeService->loadContentTypeByIdentifier(
            $this->corporateAccount->getShippingAddressContentTypeIdentifier()
        );

        $shippingAddressCreateData = (new ContentCreateMapper())->mapToFormData($contentType, [
            'mainLanguageCode' => $languageCode,
            'parentLocation' => $this->locationService->newLocationCreateStruct($parentLocation->id),
        ]);

        return $this->createContentEditForm(ShippingAddressCreateType::class, $shippingAddressCreateData, [
            'languageCode' => $languageCode,
            'mainLanguageCode' => $languageCode,
            'drafts_enabled' => false,
        ]);
    }

    public function getSetDefaultShippingAddressForm(
        Company $company,
        ?ShippingAddress $currentDefault = null
    ): FormInterface {
        return $this->formFactory->create(
            CompanyUpdateDefaultShippingAddressType::class,
            new CompanyUpdateDefaultShippingAddressData(
                $company->getContent()->contentInfo,
                $currentDefault !== null ? $currentDefault->getContent()->contentInfo : null
            )
        );
    }

    /**
     * @param \Ibexa\Contracts\CorporateAccount\Values\ShippingAddress[] $addresses
     */
    public function getDeleteShippingAddressForm(?array $addresses = null): FormInterface
    {
        return $this->formFactory->create(
            ShippingAddressItemDeleteType::class,
            new ShippingAddressItemDeleteData($addresses)
        );
    }
}
