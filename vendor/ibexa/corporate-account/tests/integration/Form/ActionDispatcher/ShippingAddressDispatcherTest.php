<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\CorporateAccount\Form\ActionDispatcher;

use Ibexa\ContentForms\Data\Content\ContentCreateData;
use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Ibexa\Contracts\CorporateAccount\Service\ShippingAddressService;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\CorporateAccount\Form\ActionDispatcher\ShippingAddressDispatcher;
use Ibexa\CorporateAccount\Form\Data\Company\CompanyUpdateDefaultShippingAddressData;
use Ibexa\CorporateAccount\Form\ShippingAddressFormFactory;
use Ibexa\FieldTypeAddress\FieldType\Value as AddressValue;
use Ibexa\Tests\Integration\CorporateAccount\IbexaKernelTestCase;

final class ShippingAddressDispatcherTest extends IbexaKernelTestCase
{
    private CompanyService $companyService;

    private ShippingAddressFormFactory $formFactory;

    private ShippingAddressDispatcher $actionDispatcher;

    private ContentTypeService $contentTypeService;

    private Company $company;

    private Content $addressBook;

    private ShippingAddressService $shippingAddressService;

    protected function setUp(): void
    {
        parent::setUp();

        self::setAdministratorUser();

        $this->formFactory = self::getShippingAddressFormFactory();
        $this->actionDispatcher = self::getShippingAddressDispatcher();
        $this->companyService = self::getCompanyService();
        $this->contentTypeService = self::getContentTypeService();
        $this->shippingAddressService = self::getShippingAddressService();

        $this->company = $this->companyService->createCompany(
            $this->getTestCompanyCreateStruct()
        );

        $this->addressBook = $this->companyService->createCompanyAddressBookFolder($this->company);

        /* Refresh after creating address book */
        $this->company = $this->companyService->getCompany($this->company->getId());
    }

    public function testCreateShippingAddress(): void
    {
        $createForm = $this->formFactory->getCreateForm(
            $this->addressBook->versionInfo->contentInfo->getMainLocation(),
            'eng-GB'
        );

        $shippingAddressContentType = $this->contentTypeService->loadContentTypeByIdentifier('shipping_address');

        $data = new ContentCreateData();
        $data->mainLanguageCode = 'eng-GB';
        $data->contentType = $shippingAddressContentType;
        $data->addFieldData(new FieldData([
            'fieldDefinition' => $shippingAddressContentType->getFieldDefinition('name'),
            'field' => new Field([
                'fieldDefIdentifier' => 'name',
                'languageCode' => 'eng-GB',
            ]),
            'value' => 'Warehouse',
        ]));
        $data->addFieldData(new FieldData([
            'fieldDefinition' => $shippingAddressContentType->getFieldDefinition('email'),
            'field' => new Field([
                'fieldDefIdentifier' => 'email',
                'languageCode' => 'eng-GB',
            ]),
            'value' => 'warehouse@foo.ltd',
        ]));
        $data->addFieldData(new FieldData([
            'fieldDefinition' => $shippingAddressContentType->getFieldDefinition('phone'),
            'field' => new Field([
                'fieldDefIdentifier' => 'phone',
                'languageCode' => 'eng-GB',
            ]),
            'value' => '987 654 321',
        ]));
        $data->addFieldData(new FieldData([
            'fieldDefinition' => $shippingAddressContentType->getFieldDefinition('address'),
            'field' => new Field([
                'fieldDefIdentifier' => 'address',
                'languageCode' => 'eng-GB',
            ]),
            'value' => new AddressValue('Warehouse', 'pl', [
                'region' => 'Lesser Poland',
                'locality' => 'Cracow',
                'street' => 'Bracka',
                'postal_code' => '31-005',
            ]),
        ]));
        $this->actionDispatcher->dispatchFormAction(
            $createForm,
            $data,
            'publish',
            ['company' => $this->company]
        );

        $shippingAddresses = $this->shippingAddressService->getCompanyShippingAddresses($this->company);
        self::assertCount(1, $shippingAddresses);

        /** @var \Ibexa\Contracts\CorporateAccount\Values\ShippingAddress $newAddress */
        $newAddress = reset($shippingAddresses);

        self::assertEquals(
            'warehouse@foo.ltd',
            $newAddress->getContent()->getFieldValue('email')->email
        );
        self::assertEquals(
            'Warehouse',
            $newAddress->getContent()->getFieldValue('address')->name
        );
        self::assertEquals(
            '31-005',
            $newAddress->getContent()->getFieldValue('address')->fields['postal_code']
        );
        self::assertNotEquals(
            $this->company->getDefaultAddressId(),
            $newAddress->getId()
        );
    }

    public function testChangeDefaultAddress(): void
    {
        $shippingFromBillingAddress = $this->shippingAddressService->createShippingAddressFromCompanyBillingAddress($this->company);
        $this->companyService->setDefaultShippingAddress($this->company, $shippingFromBillingAddress);

        $newShippingAddressCreateStruct = $this->shippingAddressService->newShippingAddressCreateStruct();

        $newShippingAddressCreateStruct->setField(
            'name',
            'Warehouse'
        );
        $newShippingAddressCreateStruct->setField(
            'email',
            'warehouse@foo.ltd'
        );
        $newShippingAddressCreateStruct->setField(
            'phone',
            '987 654 321'
        );
        $newShippingAddressCreateStruct->setField(
            'address',
            new AddressValue('Warehouse', 'pl', [
                'region' => 'Lesser Poland',
                'locality' => 'Cracow',
                'street' => 'Bracka',
                'postal_code' => '31-005',
            ])
        );

        $newShippingAddress = $this->shippingAddressService->createShippingAddress(
            $this->company,
            $newShippingAddressCreateStruct
        );

        $this->company = $this->companyService->getCompany($this->company->getId());

        self::assertEquals($shippingFromBillingAddress->getId(), $this->company->getDefaultAddressId());

        $changeDefaultForm = $this->formFactory->getSetDefaultShippingAddressForm($this->company, $shippingFromBillingAddress);

        $data = new CompanyUpdateDefaultShippingAddressData(
            $this->company->getContent()->contentInfo,
            $newShippingAddress->getContent()->contentInfo
        );

        $this->actionDispatcher->dispatchFormAction(
            $changeDefaultForm,
            $data,
            'set_as_default',
            ['company' => $this->company]
        );

        $this->company = $this->companyService->getCompany($this->company->getId());

        self::assertEquals($newShippingAddress->getId(), $this->company->getDefaultAddressId());
    }
}
