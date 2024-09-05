<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\CorporateAccount\Migrations\StepExecutor\Company;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Field as APIField;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\Section;
use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Core\FieldType\Relation\Value as RelationValue;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\Location as APILocation;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\CorporateAccount\Migrations\Generator\Company\CreateMetadata;
use Ibexa\CorporateAccount\Migrations\Generator\Company\Step\CompanyCreateStep;
use Ibexa\FieldTypeAddress\FieldType\Value as AddressValue;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\Reference\CollectorInterface;
use Ibexa\Migration\StepExecutor\StepExecutorManager;
use Ibexa\Migration\ValueObject\Content\Field;
use Ibexa\ProductCatalog\FieldType\CustomerGroup\Type;
use Ibexa\ProductCatalog\FieldType\CustomerGroup\Value as CustomerGroupValue;
use Ibexa\Tests\Integration\CorporateAccount\Migrations\StepExecutor\AbstractStepExecutorTest;

final class CompanyCreateStepExecutorTest extends AbstractStepExecutorTest
{
    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        self::setAdministratorUser();
    }

    /**
     * @dataProvider provideSteps
     */
    public function testHandle(CompanyCreateStep $step): void
    {
        $found = true;
        try {
            self::getContentService()->loadContentByRemoteId('foo_company');
        } catch (NotFoundException $e) {
            $found = false;
        }

        self::assertFalse($found);

        $executor = self::getServiceByClassName(StepExecutorManager::class);
        $executor->handle($step);

        $content = self::getContentService()->loadContentByRemoteId('foo_company');
        $company = self::getServiceByClassName(CompanyService::class)->getCompany($content->id);

        self::assertInstanceOf(Company::class, $company);

        self::assertSame('Foo Company', $company->getName());
        self::assertNotNull($company->getAddressBookId());
        self::assertNotNull($company->getMembersId());

        self::assertSame('uk', $company->getBillingAddress()->country);
        self::assertNotNull($company->getDefaultAddressId());

        $collector = self::getServiceByClassName(CollectorInterface::class);
        $references = $collector->getCollection();

        self::assertTrue($references->has('address'));
        self::assertNotNull($references->get('address')->getValue());
        self::assertTrue($references->has('members_group'));
        self::assertNotNull($references->get('members_group')->getValue());
        self::assertTrue($references->has('content'));
        self::assertNotNull($references->get('content')->getValue());
        self::assertTrue($references->has('address_location'));
        self::assertNotNull($references->get('address_location')->getValue());
        self::assertTrue($references->has('members_group_location'));
        self::assertNotNull($references->get('members_group_location')->getValue());
    }

    /**
     * @return iterable<array{\Ibexa\CorporateAccount\Migrations\Generator\Company\Step\CompanyCreateStep}>
     */
    public function provideSteps(): iterable
    {
        [$content, $fields] = $this->getCompanyInnerContent();

        $company = new Company($content);
        yield 'Default CompanyCreateStep' => [
            new CompanyCreateStep(
                CreateMetadata::createFromApi($company),
                $fields,
                [
                    new ReferenceDefinition('address', 'address_book_id'),
                    new ReferenceDefinition('members_group', 'members_group_id'),
                    new ReferenceDefinition('content', 'content_id'),
                    new ReferenceDefinition('address_location', 'address_book_location_id'),
                    new ReferenceDefinition('members_group_location', 'members_group_location_id'),
                ],
            ),
        ];
    }

    /**
     * @return array{\Ibexa\Core\Repository\Values\Content\Content, array<\Ibexa\Migration\ValueObject\Content\Field>}
     */
    protected function getCompanyInnerContent(): array
    {
        $content = new Content([
            'versionInfo' => new VersionInfo([
                'contentInfo' => new ContentInfo([
                    'remoteId' => 'foo_company',
                    'mainLanguage' => new Language([
                        'languageCode' => 'eng-GB',
                    ]),
                    'alwaysAvailable' => true,
                    'sectionId' => 2,
                    'section' => new Section([
                        'id' => 2,
                        'identifier' => 'corporate_account',
                    ]),
                    'publishedDate' => new \DateTime('2020-10-30T11:40:09+00:00'),
                    'modificationDate' => new \DateTime('2020-10-30T11:40:09+00:00'),
                    'mainLocation' => new APILocation([
                        'parentLocationId' => 1,
                        'hidden' => true,
                        'sortField' => 9,
                        'sortOrder' => 0,
                        'priority' => 1,
                    ]),
                ]),
            ]),
            'contentType' => new ContentType([
                'identifier' => 'company',
            ]),
            'internalFields' => [
                new APIField([
                    'fieldDefIdentifier' => 'name',
                    'fieldTypeIdentifier' => 'ezstring',
                    'languageCode' => 'eng-GB',
                    'value' => 'Foo Company',
                ]),
                new APIField([
                    'fieldDefIdentifier' => 'active',
                    'fieldTypeIdentifier' => 'ezbool',
                    'languageCode' => 'eng-GB',
                    'value' => true,
                ]),
                new APIField([
                    'fieldDefIdentifier' => 'vat',
                    'fieldTypeIdentifier' => 'ezstring',
                    'languageCode' => 'eng-GB',
                    'value' => '123 456 789',
                ]),
                new APIField([
                    'fieldDefIdentifier' => 'customer_group',
                    'fieldTypeIdentifier' => 'ezstring',
                    'languageCode' => 'eng-GB',
                    'value' => new CustomerGroupValue(1),
                ]),
                new APIField([
                    'fieldDefIdentifier' => 'sales_rep',
                    'fieldTypeIdentifier' => 'ezrelation',
                    'languageCode' => 'eng-GB',
                    'value' => new RelationValue(14),
                ]),
                new APIField([
                    'fieldDefIdentifier' => 'billing_address',
                    'fieldTypeIdentifier' => 'ibexa_address',
                    'languageCode' => 'eng-GB',
                    'value' => new AddressValue('HQ', 'uk'),
                ]),
            ],
        ]);

        $fieldToValueMap = [
            'name' => 'Foo Company',
            'active' => true,
            'vat' => '123 456 789',
            'customer_group' => [Type::FIELD_ID_KEY => 1],
            'sales_rep' => [
                'destinationContentId' => 14,
            ],
            'billing_address' => [
                'name' => 'HQ',
                'country' => 'uk',
                'fields' => [],
            ],
        ];

        /** @var \Ibexa\Migration\ValueObject\Content\Field[] $fields */
        $fields = [];
        foreach ($content->getFields() as $key => $field) {
            /** @phpstan-ignore-next-line */
            $fields[$key] = Field::createFromValueObject($field, $fieldToValueMap[$field->fieldDefIdentifier]);
        }

        return [$content, $fields];
    }
}
