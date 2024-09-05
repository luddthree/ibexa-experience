<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\CatalogCopy;
use Ibexa\Bundle\ProductCatalog\REST\Value\CatalogCopyStruct;
use Ibexa\Contracts\Rest\Exceptions\Parser as ParserException;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use PHPUnit\Framework\TestCase;

final class CatalogCopyTest extends TestCase
{
    private CatalogCopy $parser;

    protected function setUp(): void
    {
        $this->parser = new CatalogCopy();
    }

    public function testValidInput(): void
    {
        $catalogCopyStruct = new CatalogCopyStruct(
            'copied_catalog',
            14
        );

        $parserInput = [
            'identifier' => 'copied_catalog',
            'creator_id' => 14,
        ];

        self::assertEquals(
            $catalogCopyStruct,
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
     *             creator_id: int,
     *         },
     *     },
     * >
     */
    public function provideForTestInvalidInput(): iterable
    {
        yield [
            'Missing properties (identifier) for Catalog.',
            [
                'creator_id' => 123,
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
        $this->expectException(ParserException::class);
        $this->expectExceptionMessage($exceptionMessage);

        $this->parser->parse(
            $input,
            $this->createMock(ParsingDispatcher::class)
        );
    }
}
