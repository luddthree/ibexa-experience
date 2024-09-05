<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\CustomerGroupCreate;
use Ibexa\Bundle\ProductCatalog\REST\Value\CustomerGroupCreateStruct;
use Ibexa\Contracts\Core\Persistence\Content\Language;
use Ibexa\Contracts\Core\Persistence\Content\Language\Handler;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use PHPUnit\Framework\TestCase;

final class CustomerGroupCreateTest extends TestCase
{
    private CustomerGroupCreate $parser;

    protected function setUp(): void
    {
        $languageHandler = $this->createMock(Handler::class);
        $languageHandler->method('loadByLanguageCode')
            ->with('eng-GB')
            ->willReturn(new Language([
                'id' => 2,
                'languageCode' => 'eng-GB',
            ]));

        $this->parser = new CustomerGroupCreate($languageHandler);
    }

    public function testValidInput(): void
    {
        self::assertEquals(
            new CustomerGroupCreateStruct(
                'test-group-id',
                [
                    2 => 'test-group-name',
                ],
                [
                    2 => 'test-group-desc',
                ],
                '4',
            ),
            $this->parser->parse(
                [
                    'identifier' => 'test-group-id',
                    'names' => [
                        'eng-GB' => 'test-group-name',
                    ],
                    'descriptions' => [
                        'eng-GB' => 'test-group-desc',
                    ],
                    'global_price_rate' => '4',
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
     *             identifier?: string,
     *             name?: string,
     *             description?: string,
     *             globalPriceRate?: string,
     *         },
     *     },
     * >
     */
    public function provideForTestInvalidInput(): iterable
    {
        yield [
            'Missing properties (names, global_price_rate) for Customer Group.',
            [
                'identifier' => 'test-id',
            ],
        ];

        yield [
            'Missing properties (identifier, global_price_rate) for Customer Group.',
            [
                'names' => [
                    'eng-GB' => 'test-name',
                ],
                'descriptions' => [
                    'eng-GB' => 'test-desc',
                ],
            ],
        ];

        yield [
            'Missing properties (identifier, names) for Customer Group.',
            [
                'global_price_rate' => '90',
            ],
        ];

        yield [
            'The following properties for Customer Group are redundant: foo.',
            [
                'identifier' => 'test-id',
                'names' => [
                    'eng-GB' => 'test-name',
                ],
                'descriptions' => [
                    'eng-GB' => 'test-desc',
                ],
                'global_price_rate' => '90',
                'foo' => 'bar',
            ],
        ];
    }

    /**
     * @dataProvider provideForTestInvalidInput
     *
     * @phpstan-param string $exceptionMessage
     * @phpstan-param array{
     *     array{
     *         identifier?: string,
     *         type?: string,
     *         group?: string,
     *         names?: array<string, string>,
     *         descriptions?: array<string, string>,
     *         position?: integer,
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
