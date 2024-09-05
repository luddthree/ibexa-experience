<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\CorporateAccount\REST\Input\Parser;

use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Ibexa\Contracts\CorporateAccount\Service\MemberService;
use Ibexa\Contracts\Rest\Exceptions\InvalidArgumentException;
use Ibexa\CorporateAccount\REST\Input\Parser\MemberUpdate;
use Ibexa\Rest\Input\FieldTypeParser;
use Ibexa\Tests\CorporateAccount\PHPUnit\Value\SymfonyConstraintData;

/**
 * @covers \Ibexa\CorporateAccount\REST\Input\Parser\MemberUpdate
 */
final class MemberUpdateTest extends BaseValidatorAwareContentParserTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->parser = new MemberUpdate(
            $this->requestParserMock,
            $this->createMock(FieldTypeParser::class),
            $this->createMock(MemberService::class),
            $this->createMock(CompanyService::class),
            $this->createMock(RoleService::class),
            $this->buildSymfonyValidator()
        );
    }

    public function getDataForTestParseValidatesInputArray(): iterable
    {
        $data = [];

        yield 'Missing __url reference' => [
            $data,
            [
                $this->buildExpectedConstraintDataForMissingField('__url'),
            ],
        ];

        $data['__url'] = 'foo';
        yield 'Incorrect __url reference' => [
            $data,
            [
                new SymfonyConstraintData(
                    'This value should match REST route.',
                    '[__url]',
                    [
                        '{{ value }}' => '"foo"',
                        '{{ parserExceptionMessage }}' => InvalidArgumentException::class,
                        '{{ parserExceptionClass }}' => "No route matched 'foo'",
                    ]
                ),
            ],
        ];
    }
}
