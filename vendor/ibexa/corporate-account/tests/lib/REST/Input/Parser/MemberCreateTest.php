<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\CorporateAccount\REST\Input\Parser;

use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Contracts\CorporateAccount\Service\MemberService;
use Ibexa\Contracts\Rest\Exceptions\InvalidArgumentException;
use Ibexa\CorporateAccount\REST\Input\Parser\MemberCreate;
use Ibexa\Rest\Input\FieldTypeParser;
use Ibexa\Tests\CorporateAccount\PHPUnit\Value\SymfonyConstraintData;

/**
 * @covers \Ibexa\CorporateAccount\REST\Input\Parser\MemberCreate
 */
class MemberCreateTest extends BaseValidatorAwareContentParserTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->parser = new MemberCreate(
            $this->requestParserMock,
            $this->createMock(FieldTypeParser::class),
            $this->createMock(MemberService::class),
            $this->createMock(RoleService::class),
            $this->buildSymfonyValidator(),
        );
    }

    public function getDataForTestParseValidatesInputArray(): iterable
    {
        $data = [];
        yield 'Empty data' => [
            $data,
            [
                new SymfonyConstraintData(
                    'This field is missing.',
                    '[Role]',
                    ['{{ field }}' => '"Role"']
                ),
                new SymfonyConstraintData(
                    'This field is missing.',
                    '[__url]',
                    ['{{ field }}' => '"__url"']
                ),
                new SymfonyConstraintData(
                    'This field is missing.',
                    '[email]',
                    ['{{ field }}' => '"email"']
                ),
                new SymfonyConstraintData(
                    'This field is missing.',
                    '[login]',
                    ['{{ field }}' => '"login"']
                ),
                new SymfonyConstraintData(
                    'This field is missing.',
                    '[password]',
                    ['{{ field }}' => '"password"']
                ),
            ],
        ];

        $data['__url'] = '/corporate/companies/1/members';
        $data['login'] = 'admin';
        $data['password'] = 'publish';
        $data['email'] = 'admin@email.invalid';
        $data['Role'] = [];
        yield 'Missing Role resource href' => [
            $data,
            [
                new SymfonyConstraintData(
                    'This field is missing.',
                    '[Role][_href]',
                    ['{{ field }}' => '"_href"']
                ),
                new SymfonyConstraintData(
                    'This field is missing.',
                    '[Role][_media-type]',
                    ['{{ field }}' => '"_media-type"']
                ),
            ],
        ];

        $data['Role']['_href'] = 'foo';
        yield 'Incorrect Role resource href' => [
            $data,
            [
                new SymfonyConstraintData(
                    'This value should match REST route.',
                    '[Role][_href]',
                    [
                        '{{ value }}' => '"foo"',
                        '{{ parserExceptionMessage }}' => InvalidArgumentException::class,
                        '{{ parserExceptionClass }}' => "No route matched 'foo'",
                    ]
                ),
                $this->buildExpectedConstraintDataForMissingField(
                    '_media-type',
                    '[Role][_media-type]'
                ),
            ],
        ];
    }
}
