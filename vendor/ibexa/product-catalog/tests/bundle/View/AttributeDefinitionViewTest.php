<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\View;

use Ibexa\Bundle\ProductCatalog\View\AttributeDefinitionView;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\View\AttributeDefinitionView
 */
final class AttributeDefinitionViewTest extends TestCase
{
    public function testGetParameters(): void
    {
        $attributeDefinition = $this->createMock(AttributeDefinitionInterface::class);

        $view = new AttributeDefinitionView('example.html.twig', $attributeDefinition);
        $view->setEditable(true);

        self::assertEquals([
            'is_editable' => true,
            'attribute_definition' => $attributeDefinition,
        ], $view->getParameters());
    }
}
