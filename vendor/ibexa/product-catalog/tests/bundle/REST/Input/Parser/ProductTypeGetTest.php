<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\ProductTypeGet;
use Ibexa\Bundle\ProductCatalog\REST\Value\ProductTypeLanguageStruct;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use PHPUnit\Framework\TestCase;

final class ProductTypeGetTest extends TestCase
{
    private ProductTypeGet $parser;

    protected function setUp(): void
    {
        $this->parser = new ProductTypeGet();
    }

    public function testValidInput(): void
    {
        self::assertEquals(
            new ProductTypeLanguageStruct(['eng-GB', 'pol-PL']),
            $this->parser->parse(
                [
                    'languages' => ['eng-GB', 'pol-PL'],
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
     *             languages?: mixed,
     *             foo?: string,
     *             baz?: string,
     *         },
     *     },
     * >
     */
    public function provideForTestInvalidInput(): iterable
    {
        yield [
            'The "languages" parameter must be an array.',
            [
                'languages' => 'pol-PL',
            ],
        ];

        yield [
            'The following properties for Product Type are redundant: foo, bar.',
            [
                'foo' => 'baz',
                'bar' => 'foo',
            ],
        ];
    }

    /**
     * @dataProvider provideForTestInvalidInput
     *
     * @phpstan-param string $exceptionMessage
     * @phpstan-param array{
     *     array{
     *         languages: string[],
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
