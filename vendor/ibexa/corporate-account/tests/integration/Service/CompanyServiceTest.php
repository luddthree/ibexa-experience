<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\CorporateAccount\Service;

use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Ibexa\Contracts\Rest\Exceptions\NotFoundException;
use Ibexa\Tests\Integration\CorporateAccount\IbexaKernelTestCase;

/**
 * @covers \Ibexa\Contracts\CorporateAccount\Service\CompanyService
 */
final class CompanyServiceTest extends IbexaKernelTestCase
{
    private CompanyService $companyService;

    public function setUp(): void
    {
        parent::setUp();

        $this->companyService = self::getCompanyService();

        self::setAdministratorUser();
    }

    public function testCreateCompany(): void
    {
        $company = $this->companyService->createCompany(
            $this->getTestCompanyCreateStruct()
        );

        self::assertSame('foo ltd.', $company->getName());

        $companyInnerContent = $company->getContent();
        self::assertSame(
            self::COMPANY_CONTENT_TYPE_IDENTIFIER,
            $companyInnerContent->getContentType()->identifier
        );
        self::assertSame('eng-GB', $companyInnerContent->getDefaultLanguageCode());

        self::assertNull($company->getAddressBookId());
        self::assertNull($company->getMembersId());
        self::assertNull($company->getDefaultAddressId());
    }

    public function getCompany(): void
    {
        $company = $this->companyService->createCompany($this->getTestCompanyCreateStruct());
        $fetchedCompany = $this->companyService->getCompany($company->getId());

        self::assertSame('foo ltd.', $fetchedCompany->getName());
    }

    public function getNonCompanyContentThrowsException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->companyService->getCompany(14);
    }
}
