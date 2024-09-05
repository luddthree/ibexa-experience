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
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Ibexa\Contracts\CorporateAccount\Values\Query\Criterion\CompanyName;
use Ibexa\CorporateAccount\Form\ActionDispatcher\CompanyDispatcher;
use Ibexa\CorporateAccount\Form\CompanyFormFactory;
use Ibexa\FieldTypeAddress\FieldType\Value as AddressValue;
use Ibexa\ProductCatalog\FieldType\CustomerGroup\Value as CustomerGroupValue;
use Ibexa\Tests\Integration\CorporateAccount\IbexaKernelTestCase;

final class CompanyDispatcherTest extends IbexaKernelTestCase
{
    private LocationService $locationService;

    private CompanyService $companyService;

    private CompanyFormFactory $formFactory;

    private CompanyDispatcher $actionDispatcher;

    private ContentTypeService $contentTypeService;

    protected function setUp(): void
    {
        parent::setUp();

        self::setAdministratorUser();

        $this->formFactory = self::getCompanyFormFactory();
        $this->actionDispatcher = self::getCompanyDispatcher();
        $this->companyService = self::getCompanyService();
        $this->locationService = self::getLocationService();
        $this->contentTypeService = self::getContentTypeService();
    }

    public function testCreateCompany(): void
    {
        $createForm = $this->formFactory->getCreateForm(
            $this->locationService->loadLocationByRemoteId('corporate_account_folder'),
            'eng-GB'
        );

        $companyContentType = $this->contentTypeService->loadContentTypeByIdentifier('company');

        $data = new ContentCreateData();
        $data->mainLanguageCode = 'eng-GB';
        $data->contentType = $companyContentType;
        $data->addFieldData(new FieldData([
            'fieldDefinition' => $companyContentType->getFieldDefinition('active'),
            'field' => new Field([
                'fieldDefIdentifier' => 'active',
                'languageCode' => 'eng-GB',
            ]),
            'value' => true,
        ]));

        $data->addFieldData(new FieldData([
            'fieldDefinition' => $companyContentType->getFieldDefinition('name'),
            'field' => new Field([
                'fieldDefIdentifier' => 'name',
                'languageCode' => 'eng-GB',
            ]),
            'value' => 'bar ltd.',
        ]));
        $data->addFieldData(new FieldData([
            'fieldDefinition' => $companyContentType->getFieldDefinition('vat'),
            'field' => new Field([
                'fieldDefIdentifier' => 'vat',
                'languageCode' => 'eng-GB',
            ]),
            'value' => '123 456 789',
        ]));

        $data->addFieldData(new FieldData([
            'fieldDefinition' => $companyContentType->getFieldDefinition('customer_group'),
            'field' => new Field([
                'fieldDefIdentifier' => 'customer_group',
                'languageCode' => 'eng-GB',
            ]),
            'value' => new CustomerGroupValue(1),
        ]));
        $data->addFieldData(new FieldData([
            'fieldDefinition' => $companyContentType->getFieldDefinition('sales_rep'),
            'field' => new Field([
                'fieldDefIdentifier' => 'sales_rep',
                'languageCode' => 'eng-GB',
            ]),
            'value' => 14,
        ]));
        $data->addFieldData(new FieldData([
            'fieldDefinition' => $companyContentType->getFieldDefinition('billing_address'),
            'field' => new Field([
                'fieldDefIdentifier' => 'billing_address',
                'languageCode' => 'eng-GB',
            ]),
            'value' => new AddressValue('HQ', 'uk'),
        ]));

        $this->actionDispatcher->dispatchFormAction(
            $createForm,
            $data,
            'publish'
        );

        $companyList = $this->companyService->getCompanies(new CompanyName(Operator::CONTAINS, 'bar ltd.'));
        self::assertCount(1, $companyList);

        /** @var \Ibexa\Contracts\CorporateAccount\Values\Company $company */
        $company = reset($companyList);

        self::assertNotNull($company->getMembersId());
        self::assertNotNull($company->getAddressBookId());
        self::assertNotNull($company->getDefaultAddressId());
    }
}
