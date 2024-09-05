<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\CorporateAccount;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\CorporateAccount\CompanyService;
use Ibexa\CorporateAccount\Configuration\CorporateAccount;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Ibexa\CorporateAccount\Values\Mapper\DomainMapperInterface;
use PHPUnit\Framework\TestCase;

/**
 * Unit test case intended for covering container-configurable features difficult to cover
 * with integration tests.
 *
 * @covers \Ibexa\CorporateAccount\CompanyService
 */
final class CompanyServiceTest extends TestCase
{
    private const CUSTOM_COMPANY_CONTENT_TYPE_IDENTIFIER = 'my_company_type';

    private CompanyService $companyService;

    protected function setUp(): void
    {
        $this->companyService = $this->buildCompanyService();
    }

    public function testNewCompanyCreateStructUsesConfiguration(): void
    {
        $companyCreateStruct = $this->companyService->newCompanyCreateStruct();
        self::assertSame(
            self::CUSTOM_COMPANY_CONTENT_TYPE_IDENTIFIER,
            $companyCreateStruct->contentType->identifier
        );
        self::assertSame('eng-US', $companyCreateStruct->mainLanguageCode);
        self::assertTrue($companyCreateStruct->alwaysAvailable);
    }

    private function buildCompanyService(): CompanyService
    {
        $configResolverMock = $this->createMock(ConfigResolverInterface::class);
        $configResolverMock
            ->method('getParameter')
            ->with('languages')
            ->willReturn(['eng-US', 'pol-PL']);

        $contentTypeMock = $this->createMock(ContentType::class);
        $contentTypeMock
            ->method('__get')
            ->willReturnMap(
                [
                    ['identifier', self::CUSTOM_COMPANY_CONTENT_TYPE_IDENTIFIER],
                    ['defaultAlwaysAvailable', true],
                ]
            );
        $contentTypeService = $this->createMock(ContentTypeService::class);
        $contentTypeService
            ->method('loadContentTypeByIdentifier')
            ->with(self::CUSTOM_COMPANY_CONTENT_TYPE_IDENTIFIER)->willReturn($contentTypeMock);

        return new CompanyService(
            new CorporateAccount(
                [
                    CorporateAccountConfiguration::CONFIGURATION_CONTENT_TYPE_MAPPINGS => [
                        'company' => self::CUSTOM_COMPANY_CONTENT_TYPE_IDENTIFIER,
                    ],
                ],
                $configResolverMock
            ),
            $this->createMock(DomainMapperInterface::class),
            $this->createMock(ContentService::class),
            $contentTypeService,
            $this->createMock(LocationService::class),
            $this->createMock(UserService::class),
            $configResolverMock,
            $this->createMock(SearchService::class)
        );
    }
}
