<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\ProductCreate;
use Ibexa\Bundle\ProductCatalog\REST\Value\ProductCreateStruct;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Server\Input\Parser\ContentCreate;
use Ibexa\Rest\Server\Values\RestContentCreateStruct;
use PHPUnit\Framework\TestCase;

final class ProductCreateTest extends TestCase
{
    private ProductCreate $parser;

    private RestContentCreateStruct $restContentCreateStruct;

    protected function setUp(): void
    {
        $this->restContentCreateStruct = $this->createMock(RestContentCreateStruct::class);
        $contentCreateParser = $this->createMock(ContentCreate::class);
        $contentCreateParser->method('parse')->willReturn($this->restContentCreateStruct);

        $this->parser = new ProductCreate($contentCreateParser);
    }

    public function testValidInput(): void
    {
        $productCreateStruct = new ProductCreateStruct(
            $this->restContentCreateStruct,
            'test_code',
            [
                'identifier' => 'bar',
                'is_required' => true,
                'is_discriminator' => false,
            ]
        );

        $parserInput = [
            'ContentCreate' => [
            ],
            'code' => 'test_code',
            'attributes' => [
                'identifier' => 'bar',
                'is_required' => true,
                'is_discriminator' => false,
            ],
        ];

        self::assertEquals(
            $productCreateStruct,
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
     *             ContentCreate?: mixed,
     *             code?: string,
     *             attributes?: mixed,
     *         },
     *     },
     * >
     */
    public function provideForTestInvalidInput(): iterable
    {
        yield [
            'Missing properties (ContentCreate, code) for Product.',
            [
                'attributes' => [
                    [
                        'identifier' => 'bar',
                        'is_required' => true,
                        'is_discriminator' => false,
                    ],
                ],
            ],
        ];

        yield [
            'Missing properties (code) for Product.',
            [
                'ContentCreate' => [
                    'mainLanguageCode' => 'eng-GB',
                ],
            ],
        ];

        yield [
            'The "attributes" parameter must be an array.',
            [
                'ContentCreate' => [
                    'mainLanguageCode' => 'eng-GB',
                ],
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
