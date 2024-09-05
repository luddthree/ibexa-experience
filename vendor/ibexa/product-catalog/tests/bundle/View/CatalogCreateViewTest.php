<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\View;

use Ibexa\Bundle\ProductCatalog\View\CatalogCreateView;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

final class CatalogCreateViewTest extends TestCase
{
    public function testGetParameters(): void
    {
        $createFormView = $this->createMock(FormView::class);

        $createForm = $this->createMock(FormInterface::class);
        $createForm->method('createView')->willReturn($createFormView);

        $view = new CatalogCreateView('example.html.twig', $createForm);

        self::assertEquals([
            'form' => $createFormView,
        ], $view->getParameters());
    }
}
