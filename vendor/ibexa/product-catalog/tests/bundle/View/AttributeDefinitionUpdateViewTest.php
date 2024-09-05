<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\View;

use Ibexa\Bundle\ProductCatalog\View\AttributeDefinitionUpdateView;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\View\AttributeDefinitionUpdateView
 */
final class AttributeDefinitionUpdateViewTest extends TestCase
{
    public function testGetParameters(): void
    {
        $attributeDefinition = $this->createMock(AttributeDefinitionInterface::class);

        $updateFormView = $this->createMock(FormView::class);

        $updateForm = $this->createMock(FormInterface::class);
        $updateForm->method('createView')->willReturn($updateFormView);

        $language = $this->createMock(Language::class);

        $view = new AttributeDefinitionUpdateView(
            'edit.html.twig',
            $attributeDefinition,
            $updateForm,
            $language
        );

        self::assertEquals([
            'attribute_definition' => $attributeDefinition,
            'form' => $updateFormView,
            'language' => $language,
        ], $view->getParameters());
    }
}
