<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\CorporateAccount\Cdp\Export\User;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Ibexa\Contracts\CorporateAccount\Service\MemberService;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\FieldType\User\Value;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\CorporateAccount\Cdp\Export\User\CompanyInfoUserItemProcessor;
use Ibexa\CorporateAccount\Values\MemberAssignment;
use PHPUnit\Framework\TestCase;

final class CompanyInfoUserItemProcessorTest extends TestCase
{
    /** @var \Ibexa\Contracts\CorporateAccount\Service\MemberService|\PHPUnit\Framework\MockObject\MockObject */
    private MemberService $memberService;

    /** @var \Ibexa\Contracts\CorporateAccount\Service\CompanyService|\PHPUnit\Framework\MockObject\MockObject */
    private CompanyService $companyService;

    private CompanyInfoUserItemProcessor $companyInfoUserItemProcessor;

    protected function setUp(): void
    {
        $this->memberService = $this->createMock(MemberService::class);
        $this->companyService = $this->createMock(CompanyService::class);

        $this->companyInfoUserItemProcessor = new CompanyInfoUserItemProcessor(
            $this->memberService,
            $this->companyService,
            'ibexa_user',
        );
    }

    public function testProcess(): void
    {
        $processedItemData = ['id' => 1];

        /** @var \Ibexa\Core\Repository\Values\Content\Content|\PHPUnit\Framework\MockObject\MockObject $contentMock */
        $contentMock = $this->createMock(Content::class);
        $contentMock
            ->method('getFields')
            ->willReturn([
                new Field([
                    'fieldTypeIdentifier' => 'ibexa_user',
                    'value' => new Value([
                        'login' => 'test',
                        'email' => 'test@example.com',
                    ]),
                ]),
            ]);
        $contentMock
            ->method('__get')
            ->with('id')
            ->willReturn(1);

        $this->memberService
            ->method('getMemberAssignmentsByMemberId')
            ->willReturn([new MemberAssignment(
                1,
                1,
                'test',
                1,
                1,
                1,
            )]);

        $this->companyService
            ->method('getCompany')
            ->with(1)
            ->willReturn(
                new Company(
                    new Content([
                        'versionInfo' => new VersionInfo([
                            'contentInfo' => new ContentInfo(['id' => 1]),
                            'names' => ['eng-GB' => 'Ibexa'],
                            'initialLanguageCode' => 'eng-GB',
                        ]),
                    ]),
                ),
            );

        $processedItemData = $this->companyInfoUserItemProcessor->process(
            $processedItemData,
            $contentMock,
        );

        self::assertSame(
            [
                'id' => 1,
                'companies' => [
                    ['company_id' => 1, 'company_name' => 'Ibexa'],
                ],
            ],
            $processedItemData,
        );
    }

    public function testProcessThrowsExceptionOnNoUserField(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Argument '\$item' is invalid: Item does not contain user field");

        $processedItemData = ['id' => 1];

        /** @var \Ibexa\Core\Repository\Values\Content\Content|\PHPUnit\Framework\MockObject\MockObject $contentMock */
        $contentMock = $this->createMock(Content::class);
        $contentMock
            ->method('getFields')
            ->willReturn([]);

        $this->companyInfoUserItemProcessor->process(
            $processedItemData,
            $contentMock,
        );
    }
}
