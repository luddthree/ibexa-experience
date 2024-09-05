<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\ProductTypeCreate;
use Ibexa\Bundle\ProductCatalog\REST\Value\ProductTypeCreateStruct;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\AssignAttributeDefinitionStruct;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Core\Repository\Values\ContentType\ContentTypeCreateStruct;
use Ibexa\Rest\Server\Input\Parser\ContentTypeCreate;
use PHPUnit\Framework\TestCase;

final class ProductTypeCreateTest extends TestCase
{
    private ProductTypeCreate $parser;

    protected function setUp(): void
    {
        $contentTypeCreateParser = $this->createMock(ContentTypeCreate::class);
        $contentTypeCreateParser->method('parse')->willReturn(new ContentTypeCreateStruct());

        $attributeDefinitionService =
            $this->createMock(AttributeDefinitionServiceInterface::class);

        $attributeDefinitionService
            ->method('getAttributeDefinition')
            ->willReturn(
                $this->createMock(AttributeDefinitionInterface::class)
            );

        $this->parser = new ProductTypeCreate(
            $contentTypeCreateParser,
            $attributeDefinitionService
        );
    }

    public function testValidInput(): void
    {
        $productTypeCreateStruct = new ProductTypeCreateStruct(
            new ContentTypeCreateStruct(),
            'eng-GB',
            [
                new AssignAttributeDefinitionStruct(
                    $this->createMock(AttributeDefinitionInterface::class),
                    true,
                    true
                ),
            ]
        );

        $parserInput = [
            'ContentTypeCreateStruct' => [
            ],
            'main_language_code' => 'eng-GB',
            'assigned_attributes' => [
                [
                    'identifier' => 'bar',
                    'is_required' => true,
                    'is_discriminator' => true,
                ],
            ],
        ];

        self::assertEquals(
            $productTypeCreateStruct,
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
     *             ContentTypeCreateStruct?: mixed,
     *             main_language_code?: string,
     *             assigned_attributes?: mixed,
     *         },
     *     },
     * >
     */
    public function provideForTestInvalidInput(): iterable
    {
        yield [
            'Missing properties (main_language_code) for Product Type.',
            [
                'ContentTypeCreateStruct' => [
                    'names' => [
                        'eng-GB' => 'test',
                    ],
                ],
                'assigned_attributes' => [
                    [
                        'identifier' => 'bar',
                        'is_required' => true,
                        'is_discriminator' => false,
                    ],
                ],
            ],
        ];

        yield [
            'Missing properties (ContentTypeCreateStruct) for Product Type.',
            [
                'main_language_code' => 'eng-GB',
            ],
        ];

        yield [
            'The "assigned_attributes" parameter must be an array.',
            [
                'ContentTypeCreateStruct' => [
                    'names' => [
                        'eng-GB' => 'test',
                    ],
                ],
                'main_language_code' => 'eng-GB',
                'assigned_attributes' => 'foo',
            ],
        ];
    }

    /**
     * @dataProvider provideForTestInvalidInput
     *
     * @phpstan-param string $exceptionMessage
     * @phpstan-param array{
     *     array{
     *         ContentTypeCreateStruct?: mixed,
     *         main_language_code?: string,
     *         assigned_attributes?: mixed,
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
