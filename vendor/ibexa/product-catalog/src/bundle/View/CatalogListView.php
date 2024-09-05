<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\View;

use Ibexa\Core\MVC\Symfony\View\BaseView;
use Symfony\Component\Form\FormInterface;

final class CatalogListView extends BaseView
{
    /** @var iterable<\Ibexa\Contracts\ProductCatalog\Values\CatalogInterface> */
    private iterable $catalogs;

    private FormInterface $searchForm;

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Values\CatalogInterface> $catalogs
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentType
     */
    public function __construct(
        $templateIdentifier,
        iterable $catalogs,
        FormInterface $searchForm
    ) {
        parent::__construct($templateIdentifier);

        $this->catalogs = $catalogs;
        $this->searchForm = $searchForm;
    }

    /**
     * @return iterable<\Ibexa\Contracts\ProductCatalog\Values\CatalogInterface>
     */
    public function getCatalogs(): iterable
    {
        return $this->catalogs;
    }

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Values\CatalogInterface> $catalogs
     */
    public function setCatalogs(iterable $catalogs): void
    {
        $this->catalogs = $catalogs;
    }

    public function getSearchForm(): FormInterface
    {
        return $this->searchForm;
    }

    public function setSearchForm(FormInterface $searchForm): void
    {
        $this->searchForm = $searchForm;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getInternalParameters(): array
    {
        return [
            'catalogs' => $this->catalogs,
            'search_form' => $this->searchForm->createView(),
        ];
    }
}
