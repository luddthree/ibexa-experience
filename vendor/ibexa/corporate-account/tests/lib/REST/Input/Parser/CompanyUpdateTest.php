<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\CorporateAccount\REST\Input\Parser;

use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Ibexa\Contracts\Rest\Exceptions\InvalidArgumentException;
use Ibexa\CorporateAccount\REST\Input\Parser\CompanyUpdate;
use Ibexa\Rest\Input\FieldTypeParser;
use Ibexa\Tests\CorporateAccount\PHPUnit\Value\SymfonyConstraintData;

/**
 * @covers \Ibexa\CorporateAccount\REST\Input\Parser\CompanyUpdate
 */
final class CompanyUpdateTest extends BaseValidatorAwareContentParserTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->parser = new CompanyUpdate(
            $this->requestParserMock,
            $this->createMock(CompanyService::class),
            $this->createMock(FieldTypeParser::class),
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
                $this->buildExpectedConstraintDataForMissingField('fields'),
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
                $this->buildExpectedConstraintDataForMissingField('fields'),
            ],
        ];

        $data['__url'] = '/corporate/companies/1';
        yield 'Missing fields list' => [
            $data,
            [
                $this->buildExpectedConstraintDataForMissingField('fields'),
            ],
        ];

        $data['fields'] = 'foo';
        yield 'Incorrect fields structure' => [
            $data,
            [
                new SymfonyConstraintData(
                    'This value should be of type array.',
                    '[fields]',
                    [
                        '{{ value }}' => '"foo"',
                        '{{ type }}' => 'array',
                    ]
                ),
            ],
        ];

        $data['fields'] = [];
        yield 'Empty fields structure' => [
            $data,
            [
                new SymfonyConstraintData(
                    'This collection should contain 1 element or more.',
                    '[fields]',
                    [
                        '{{ count }}' => '0',
                        '{{ limit }}' => '1',
                    ]
                ),
            ],
        ];
    }
}
