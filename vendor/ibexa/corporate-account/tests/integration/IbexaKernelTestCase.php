<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\CorporateAccount;

use Ibexa\Contracts\Core\Test\IbexaKernelTestCase as BaseIbexaKernelTestCase;
use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Ibexa\Contracts\CorporateAccount\Service\CorporateAccountService;
use Ibexa\Contracts\CorporateAccount\Service\MemberService;
use Ibexa\Contracts\CorporateAccount\Service\SalesRepresentativesService;
use Ibexa\Contracts\CorporateAccount\Service\ShippingAddressService;
use Ibexa\Contracts\CorporateAccount\Values\CompanyCreateStruct;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Ibexa\CorporateAccount\Form\ActionDispatcher\CompanyDispatcher;
use Ibexa\CorporateAccount\Form\ActionDispatcher\MemberDispatcher;
use Ibexa\CorporateAccount\Form\ActionDispatcher\ShippingAddressDispatcher;
use Ibexa\CorporateAccount\Form\CompanyFormFactory;
use Ibexa\CorporateAccount\Form\MemberFormFactory;
use Ibexa\CorporateAccount\Form\ShippingAddressFormFactory;
use Ibexa\FieldTypeAddress\FieldType\Value as AddressValue;
use Ibexa\ProductCatalog\FieldType\CustomerGroup\Value as CustomerGroupValue;
use League\Flysystem\FilesystemOperator;
use Webmozart\Assert\Assert;

abstract class IbexaKernelTestCase extends BaseIbexaKernelTestCase
{
    public const COMPANY_CONTENT_TYPE_IDENTIFIER = 'company';

    protected function setUp(): void
    {
        self::bootKernel();
    }

    protected static function getCorporateAccountConfiguration(): CorporateAccountConfiguration
    {
        return self::getServiceByClassName(CorporateAccountConfiguration::class);
    }

    protected static function getCompanyService(): CompanyService
    {
        return self::getServiceByClassName(CompanyService::class);
    }

    protected static function getMemberService(): MemberService
    {
        return self::getServiceByClassName(MemberService::class);
    }

    protected static function getShippingAddressService(): ShippingAddressService
    {
        return self::getServiceByClassName(ShippingAddressService::class);
    }

    protected static function getCorporateAccountService(): CorporateAccountService
    {
        return self::getServiceByClassName(CorporateAccountService::class);
    }

    protected static function getSalesRepresentativesService(): SalesRepresentativesService
    {
        return self::getServiceByClassName(SalesRepresentativesService::class);
    }

    protected static function getCompanyFormFactory(): CompanyFormFactory
    {
        return self::getServiceByClassName(CompanyFormFactory::class);
    }

    protected static function getMemberFormFactory(): MemberFormFactory
    {
        return self::getServiceByClassName(MemberFormFactory::class);
    }

    protected static function getShippingAddressFormFactory(): ShippingAddressFormFactory
    {
        return self::getServiceByClassName(ShippingAddressFormFactory::class);
    }

    protected static function getCompanyDispatcher(): CompanyDispatcher
    {
        return self::getServiceByClassName(CompanyDispatcher::class);
    }

    protected static function getMemberDispatcher(): MemberDispatcher
    {
        return self::getServiceByClassName(MemberDispatcher::class);
    }

    protected static function getShippingAddressDispatcher(): ShippingAddressDispatcher
    {
        return self::getServiceByClassName(ShippingAddressDispatcher::class);
    }

    protected static function getFilesystem(): FilesystemOperator
    {
        return self::getServiceByClassName(
            FilesystemOperator::class,
            'ibexa.migrations.io.flysystem.default_filesystem'
        );
    }

    protected function executeMigration(string $name): void
    {
        self::assertInstanceOf(IbexaTestKernel::class, self::$kernel);
        self::$kernel->executeMigration($name);
    }

    final protected static function loadFile(string $filepath): string
    {
        $expected = file_get_contents($filepath);
        Assert::notFalse($expected, sprintf(
            'File "%s" is missing or not readable',
            $filepath,
        ));

        return $expected;
    }

    protected function getTestCompanyCreateStruct(): CompanyCreateStruct
    {
        $companyService = self::getCompanyService();
        $companyCreateStruct = $companyService->newCompanyCreateStruct();
        $companyCreateStruct->setField('active', true);
        $companyCreateStruct->setField('name', 'foo ltd.');
        $companyCreateStruct->setField('vat', '123 456 789');
        $companyCreateStruct->setField('customer_group', new CustomerGroupValue(1));
        $companyCreateStruct->setField('sales_rep', 14);
        $companyCreateStruct->setField('billing_address', new AddressValue('HQ', 'uk'));

        return $companyCreateStruct;
    }
}
