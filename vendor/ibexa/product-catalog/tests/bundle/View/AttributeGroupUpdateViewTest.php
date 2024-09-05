<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\View;

use Ibexa\Bundle\ProductCatalog\View\AttributeGroupUpdateView;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

final class AttributeGroupUpdateViewTest extends TestCase
{
    public function testGetParameters(): void
    {
        $attributeGroup = $this->createMock(AttributeGroupInterface::class);

        $updateFormView = $this->createMock(FormView::class);

        $updateForm = $this->createMock(FormInterface::class);
        $updateForm->method('createView')->willReturn($updateFormView);

        $view = new AttributeGroupUpdateView('example.html.twig', $attributeGroup, $updateForm);

        self::assertEquals([
            'attribute_group' => $attributeGroup,
            'form' => $updateFormView,
        ], $view->getParameters());
    }
}
