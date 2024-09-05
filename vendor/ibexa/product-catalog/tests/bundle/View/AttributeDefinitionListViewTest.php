<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\View;

use Ibexa\Bundle\ProductCatalog\View\AttributeDefinitionListView;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\View\AttributeDefinitionListView
 */
final class AttributeDefinitionListViewTest extends TestCase
{
    public function testGetParameters(): void
    {
        $attributeDefinitions = [
            $this->createMock(AttributeDefinitionInterface::class),
            $this->createMock(AttributeDefinitionInterface::class),
            $this->createMock(AttributeDefinitionInterface::class),
        ];

        $searchFormView = $this->createMock(FormView::class);

        $searchForm = $this->createMock(FormInterface::class);
        $searchForm->method('createView')->willReturn($searchFormView);

        $view = new AttributeDefinitionListView('example.html.twig', $attributeDefinitions, $searchForm);
        $view->setEditable(true);

        self::assertEquals(
            [
                'attributes_definitions' => $attributeDefinitions,
                'is_editable' => true,
                'search_form' => $searchFormView,
            ],
            $view->getParameters()
        );
    }
}
