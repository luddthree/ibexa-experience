<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\CustomerGroupUpdate;
use Ibexa\Bundle\ProductCatalog\REST\Value\CustomerGroupUpdateStruct;
use Ibexa\Contracts\Core\Persistence\Content\Language;
use Ibexa\Contracts\Core\Persistence\Content\Language\Handler;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use PHPUnit\Framework\TestCase;

final class CustomerGroupUpdateTest extends TestCase
{
    private CustomerGroupUpdate $parser;

    protected function setUp(): void
    {
        $languageHandler = $this->createMock(Handler::class);
        $languageHandler->method('loadByLanguageCode')
            ->with('eng-GB')
            ->willReturn(new Language([
                'id' => 2,
                'languageCode' => 'eng-GB',
            ]));

        $this->parser = new CustomerGroupUpdate($languageHandler);
    }

    public function testValidInput(): void
    {
        self::assertEquals(
            new CustomerGroupUpdateStruct(
                'customer-group-id',
                [
                    2 => 'customer-group-name',
                ],
                [
                    2 => 'customer-group-desc',
                ],
                '80',
            ),
            $this->parser->parse(
                [
                    'identifier' => 'customer-group-id',
                    'names' => [
                        'eng-GB' => 'customer-group-name',
                    ],
                    'descriptions' => [
                        'eng-GB' => 'customer-group-desc',
                    ],
                    'global_price_rate' => '80',
                ],
                $this->createMock(ParsingDispatcher::class)
            )
        );

        self::assertEquals(
            new CustomerGroupUpdateStruct('customer-group-id2'),
            $this->parser->parse(
                [
                    'identifier' => 'customer-group-id2',
                ],
                $this->createMock(ParsingDispatcher::class)
            )
        );
    }

    /**
     * @phpstan-return iterable<
     *     array{
     *         string,
     *         array{
     *             foo: string,
     *             baz: string,
     *         },
     *     },
     * >
     */
    public function provideForTestInvalidInput(): iterable
    {
        yield [
            'The following properties for Customer Group are redundant: foo, baz',
            [
                'foo' => 'bar',
                'baz' => 'foo',
            ],
        ];
    }

    /**
     * @dataProvider provideForTestInvalidInput
     *
     * @phpstan-param string $exceptionMessage
     * @phpstan-param array{
     *     array{
     *         names: string[],
     *         identifier?: string,
     *         position?: integer,
     *         languages?: string,
     *     },
     * } $input
     */
    public function testInvalidInput(string $exceptionMessage, array $input): void
    {
        $this->expectException(Parser::class);
        $this->expectExceptionMessage($exceptionMessage);

        $this->parser->parse(
            $input,
            $this->createMock(ParsingDispatcher::class)
        );
    }
}
