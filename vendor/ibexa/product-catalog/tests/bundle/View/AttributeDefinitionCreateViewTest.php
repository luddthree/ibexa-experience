<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\View;

use Ibexa\Bundle\ProductCatalog\View\AttributeDefinitionCreateView;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\View\AttributeDefinitionCreateView
 */
final class AttributeDefinitionCreateViewTest extends TestCase
{
    public function testGetParameters(): void
    {
        $createFormView = $this->createMock(FormView::class);

        $createForm = $this->createMock(FormInterface::class);
        $createForm->method('createView')->willReturn($createFormView);

        $attributeType = $this->createMock(AttributeTypeInterface::class);
        $language = $this->createMock(Language::class);

        $view = new AttributeDefinitionCreateView('create.html.twig', $createForm, $attributeType, $language);

        self::assertEquals([
            'attribute_type' => $attributeType,
            'form' => $createFormView,
            'language' => $language,
        ], $view->getParameters());
    }
}
