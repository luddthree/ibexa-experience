<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\View;

use Ibexa\Core\MVC\Symfony\View\BaseView;
use Symfony\Component\Form\FormInterface;

final class CurrencyListView extends BaseView
{
    /** @var iterable<\Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface> */
    private iterable $currencies;

    private FormInterface $searchForm;

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface> $currencies
     */
    public function __construct($templateIdentifier, iterable $currencies, FormInterface $searchForm)
    {
        parent::__construct($templateIdentifier);

        $this->currencies = $currencies;
        $this->searchForm = $searchForm;
    }

    /**
     * @return iterable<\Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface>
     */
    public function getCurrencies(): iterable
    {
        return $this->currencies;
    }

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface> $currencies
     */
    public function setCurrencies(iterable $currencies): void
    {
        $this->currencies = $currencies;
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
            'currencies' => $this->currencies,
            'search_form' => $this->searchForm->createView(),
        ];
    }
}
