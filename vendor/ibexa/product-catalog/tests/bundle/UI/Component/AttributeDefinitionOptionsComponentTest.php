<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\UI\Component;

use Ibexa\Bundle\ProductCatalog\UI\Component\AttributeDefinitionOptionsComponent;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinition;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeType;
use Ibexa\ProductCatalog\Local\Repository\Values\ProductList;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class AttributeDefinitionOptionsComponentTest extends TestCase
{
    /**
     * @dataProvider provideForTestRender
     *
     * @param array<string,mixed> $parameters
     */
    public function testRender(string $attributeType, array $parameters, string $expectedResult): void
    {
        $component = new AttributeDefinitionOptionsComponent(
            $this->getEnvironment(),
            $attributeType,
            'attribute_definition_options.html.twig'
        );

        self::assertEquals($expectedResult, $component->render($parameters));
    }

    /**
     * @return iterable<string, array{
     *     string,
     *     array<string,mixed>,
     *     string
     * }>
     */
    public function provideForTestRender(): iterable
    {
        yield 'empty parameters' => [
            'selection',
            [],
            '',
        ];

        yield 'null attribute definition' => [
            'checkbox',
            [
                'attribute_definition' => null,
            ],
            '',
        ];

        yield 'wrong attribute definition instance' => [
            'selection',
            [
                'attribute_definition' => new ProductList(),
            ],
            '',
        ];

        yield 'yet unsupported attribute definition instance' => [
            'float',
            [
                'attribute_definition' => $this->getAttributeDefinition(),
            ],
            '',
        ];

        yield 'actual attribute definition instance' => [
            'selection',
            [
                'attribute_definition' => $this->getAttributeDefinition(),
            ],
            'option1 option1_value option2 option2_value option3 option3_value ',
        ];
    }

    private function getAttributeDefinition(): AttributeDefinitionInterface
    {
        return new AttributeDefinition(
            1,
            'foo',
            new AttributeType('selection'),
            new AttributeGroup(1, 'foo', 'Foo', 0, [], []),
            'name',
            0,
            [],
            'description',
            [],
            [],
            [
                'option1' => 'option1_value',
                'option2' => 'option2_value',
                'option3' => 'option3_value',
            ]
        );
    }

    private function getEnvironment(): Environment
    {
        return new Environment(
            new FilesystemLoader(__DIR__ . '/templates/')
        );
    }
}
