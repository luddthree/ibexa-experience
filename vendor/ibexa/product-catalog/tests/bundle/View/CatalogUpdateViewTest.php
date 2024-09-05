<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\View;

use Ibexa\Bundle\ProductCatalog\View\CatalogUpdateView;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

final class CatalogUpdateViewTest extends TestCase
{
    public function testGetParameters(): void
    {
        $catalog = $this->createMock(CatalogInterface::class);

        $updateFormView = $this->createMock(FormView::class);

        $updateForm = $this->createMock(FormInterface::class);
        $updateForm->method('createView')->willReturn($updateFormView);

        $view = new CatalogUpdateView('example.html.twig', $catalog, $updateForm, []);

        self::assertEquals([
            'catalog' => $catalog,
            'form' => $updateFormView,
            'products' => [],
        ], $view->getParameters());
    }
}
