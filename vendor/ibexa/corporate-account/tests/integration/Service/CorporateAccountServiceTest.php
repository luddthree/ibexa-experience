<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\CorporateAccount\Service;

use Ibexa\Contracts\CorporateAccount\Service\CorporateAccountService;
use Ibexa\Tests\Integration\CorporateAccount\IbexaKernelTestCase;

/**
 * @covers \Ibexa\Contracts\CorporateAccount\Service\CorporateAccountService
 * @covers \Ibexa\CorporateAccount\CorporateAccountService
 */
final class CorporateAccountServiceTest extends IbexaKernelTestCase
{
    private CorporateAccountService $corporateAccountService;

    public function setUp(): void
    {
        parent::setUp();

        $this->corporateAccountService = self::getCorporateAccountService();

        self::setAdministratorUser();
    }

    public function testCreateCompany(): void
    {
        $company = $this->corporateAccountService->createCompany(
            $this->getTestCompanyCreateStruct()
        );

        self::assertNotNull(
            $company->getAddressBookId(),
            'A company does not have an address book'
        );
        self::assertNotNull($company->getMembersId(), 'A company does not have members user group');
        self::assertNotNull(
            $company->getDefaultAddressId(),
            'A company does not have default address'
        );
    }
}
