<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\ProductTypeUpdate;
use Ibexa\Bundle\ProductCatalog\REST\Value\ProductTypeUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeUpdateStruct;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\AssignAttributeDefinitionStruct;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Server\Input\Parser\ContentTypeUpdate;
use PHPUnit\Framework\TestCase;

final class ProductTypeUpdateTest extends TestCase
{
    private ProductTypeUpdate $parser;

    protected function setUp(): void
    {
        $contentTypeCreateParser = $this->createMock(ContentTypeUpdate::class);
        $contentTypeCreateParser->method('parse')->willReturn(new ContentTypeUpdateStruct());

        $attributeDefinitionService =
            $this->createMock(AttributeDefinitionServiceInterface::class);

        $attributeDefinitionService
            ->method('getAttributeDefinition')
            ->willReturn(
                $this->createMock(AttributeDefinitionInterface::class)
            );

        $this->parser = new ProductTypeUpdate(
            $contentTypeCreateParser,
            $attributeDefinitionService
        );
    }

    public function testValidInput(): void
    {
        $productTypeCreateStruct = new ProductTypeUpdateStruct(
            new ContentTypeUpdateStruct(),
            [
                new AssignAttributeDefinitionStruct(
                    $this->createMock(AttributeDefinitionInterface::class),
                    true,
                    true
                ),
            ]
        );

        $parserInput = [
            'ContentTypeUpdateStruct' => [
            ],
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
}
