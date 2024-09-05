<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\CurrencyCreate;
use Ibexa\Bundle\ProductCatalog\REST\Value\CurrencyCreateStruct;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use PHPUnit\Framework\TestCase;

final class CurrencyCreateTest extends TestCase
{
    private CurrencyCreate $parser;

    protected function setUp(): void
    {
        $this->parser = new CurrencyCreate();
    }

    public function testValidInput(): void
    {
        self::assertEquals(
            new CurrencyCreateStruct('USD', 2, true),
            $this->parser->parse(
                [
                    'code' => 'USD',
                    'subunits' => 2,
                    'enabled' => true,
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
     *             code?: string,
     *             subunits?: int<0, max>,
     *             enabled?: bool,
     *         },
     *     },
     * >
     */
    public function provideForTestInvalidInput(): iterable
    {
        yield [
            'Missing properties (code, subunits, enabled) for Currency.',
            [],
        ];

        yield [
            'Missing properties (code) for Currency.',
            [
                'subunits' => 3,
                'enabled' => false,
            ],
        ];

        yield [
            'Missing properties (subunits) for Currency.',
            [
                'code' => 'EUR',
                'enabled' => true,
            ],
        ];

        yield [
            'Missing properties (enabled) for Currency.',
            [
                'code' => 'GBP',
                'subunits' => 2,
            ],
        ];
    }

    /**
     * @dataProvider provideForTestInvalidInput
     *
     * @phpstan-param string $exceptionMessage
     * @phpstan-param array{
     *     array{
     *         code?: string,
     *         subunits?: int<0, max>,
     *         enabled?: bool,
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
