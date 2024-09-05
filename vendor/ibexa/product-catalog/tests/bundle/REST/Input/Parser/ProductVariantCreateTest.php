<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\ProductVariantCreate;
use Ibexa\Bundle\ProductCatalog\REST\Value\ProductVariantCreateStruct;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use PHPUnit\Framework\TestCase;

final class ProductVariantCreateTest extends TestCase
{
    private ProductVariantCreate $parser;

    protected function setUp(): void
    {
        $this->parser = new ProductVariantCreate();
    }

    public function testValidInput(): void
    {
        $productVariantCreateStruct = new ProductVariantCreateStruct(
            [
                'test' => 'bar',
                'foo' => 'baz',
            ],
            'test_code'
        );

        $parserInput = [
            'code' => 'test_code',
            'attributes' => [
                'test' => 'bar',
                'foo' => 'baz',
            ],
        ];

        self::assertEquals(
            $productVariantCreateStruct,
            $this->parser->parse(
                $parserInput,
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
     *             attributes?: mixed,
     *         },
     *     },
     * >
     */
    public function provideForTestInvalidInput(): iterable
    {
        yield [
            'Missing properties (code) for Product Variant.',
            [
                'attributes' => [
                    [
                        'foo' => 'bar',
                    ],
                ],
            ],
        ];

        yield [
            'Missing properties (attributes) for Product Variant.',
            [
                'code' => 'baz',
            ],
        ];

        yield [
            'The "attributes" parameter must be an array.',
            [
                'code' => 'red',
                'attributes' => 'foo',
            ],
        ];
    }

    /**
     * @dataProvider provideForTestInvalidInput
     *
     * @phpstan-param string $exceptionMessage
     * @phpstan-param array{
     *     array{
     *         ContentCreate?: mixed,
     *         code?: string,
     *         attributes?: mixed,
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
