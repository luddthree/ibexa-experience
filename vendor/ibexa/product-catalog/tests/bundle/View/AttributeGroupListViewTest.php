<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\View;

use Ibexa\Bundle\ProductCatalog\View\AttributeGroupListView;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

final class AttributeGroupListViewTest extends TestCase
{
    public function testGetParameters(): void
    {
        $attributeGroups = [
            $this->createMock(AttributeGroupInterface::class),
            $this->createMock(AttributeGroupInterface::class),
            $this->createMock(AttributeGroupInterface::class),
        ];

        $searchFormView = $this->createMock(FormView::class);

        $searchForm = $this->createMock(FormInterface::class);
        $searchForm->method('createView')->willReturn($searchFormView);

        $view = new AttributeGroupListView('example.html.twig', $attributeGroups, $searchForm);
        $view->setEditable(true);

        self::assertEquals(
            [
                'is_editable' => true,
                'search_form' => $searchFormView,
                'attribute_groups' => $attributeGroups,
            ],
            $view->getParameters()
        );
    }
}
