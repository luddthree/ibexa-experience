<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\View;

use Ibexa\Bundle\ProductCatalog\View\CatalogListView;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

final class CatalogListViewTest extends TestCase
{
    public function testGetParameters(): void
    {
        $catalogs = [
            $this->createMock(CatalogInterface::class),
            $this->createMock(CatalogInterface::class),
            $this->createMock(CatalogInterface::class),
        ];

        $searchFormView = $this->createMock(FormView::class);

        $searchForm = $this->createMock(FormInterface::class);
        $searchForm->method('createView')->willReturn($searchFormView);

        $view = new CatalogListView('example.html.twig', $catalogs, $searchForm);

        self::assertEquals(
            [
                'search_form' => $searchFormView,
                'catalogs' => $catalogs,
            ],
            $view->getParameters()
        );
    }
}
