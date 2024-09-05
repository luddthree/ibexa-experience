<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\CorporateAccount\REST\Input\Parser;

use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Ibexa\Contracts\Rest\Exceptions\InvalidArgumentException;
use Ibexa\CorporateAccount\REST\Input\Parser\CompanyCreate;
use Ibexa\Rest\Input\FieldTypeParser;
use Ibexa\Tests\CorporateAccount\PHPUnit\Value\SymfonyConstraintData;

/**
 * @covers \Ibexa\CorporateAccount\REST\Input\Parser\CompanyCreate
 */
final class CompanyCreateTest extends BaseValidatorAwareContentParserTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->parser = new CompanyCreate(
            $this->requestParserMock,
            $this->createMock(CompanyService::class),
            $this->createMock(FieldTypeParser::class),
            $this->buildSymfonyValidator(),
        );
    }

    public function getDataForTestParseValidatesInputArray(): iterable
    {
        $data = ['__url' => '/corporate/companies'];

        $data['User'] = [];
        yield 'Missing User resource _href' => [
            $data,
            [
                $this->buildExpectedConstraintDataForMissingField('_href', '[User][_href]'),
                $this->buildExpectedConstraintDataForMissingField('_media-type', '[User][_media-type]'),
            ],
        ];

        $data['User']['_href'] = 'foo';
        yield 'Incorrect User resource href' => [
            $data,
            [
                new SymfonyConstraintData(
                    'This value should match REST route.',
                    '[User][_href]',
                    [
                        '{{ value }}' => '"foo"',
                        '{{ parserExceptionMessage }}' => InvalidArgumentException::class,
                        '{{ parserExceptionClass }}' => "No route matched 'foo'",
                    ]
                ),
                $this->buildExpectedConstraintDataForMissingField('_media-type', '[User][_media-type]'),
            ],
        ];
    }
}
