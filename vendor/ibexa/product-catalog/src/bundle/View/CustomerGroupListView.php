<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\View;

use Ibexa\Core\MVC\Symfony\View\BaseView;
use Symfony\Component\Form\FormInterface;

final class CustomerGroupListView extends BaseView
{
    /** @var iterable<\Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface> */
    private iterable $customerGroups;

    private FormInterface $searchForm;

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface> $customerGroups
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentType
     */
    public function __construct($templateIdentifier, iterable $customerGroups, FormInterface $searchForm)
    {
        parent::__construct($templateIdentifier);

        $this->customerGroups = $customerGroups;
        $this->searchForm = $searchForm;
    }

    /**
     * @return iterable<\Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface>
     */
    public function getCustomerGroups(): iterable
    {
        return $this->customerGroups;
    }

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface> $customerGroups
     */
    public function setCustomerGroups(iterable $customerGroups): void
    {
        $this->customerGroups = $customerGroups;
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
            'customer_groups' => $this->customerGroups,
            'search_form' => $this->searchForm->createView(),
        ];
    }
}
