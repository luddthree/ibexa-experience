<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\View;

use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Symfony\Component\Form\FormInterface;

final class CustomerGroupUpdateView extends BaseView
{
    private CustomerGroupInterface $customerGroup;

    private FormInterface $form;

    public function __construct(string $templateIdentifier, CustomerGroupInterface $customerGroup, FormInterface $form)
    {
        parent::__construct($templateIdentifier);

        $this->customerGroup = $customerGroup;
        $this->form = $form;
    }

    public function getCustomerGroup(): CustomerGroupInterface
    {
        return $this->customerGroup;
    }

    public function setCustomerGroup(CustomerGroupInterface $customerGroup): void
    {
        $this->customerGroup = $customerGroup;
    }

    public function getForm(): FormInterface
    {
        return $this->form;
    }

    public function setForm(FormInterface $form): void
    {
        $this->form = $form;
    }

    /**
     * @return array<string,mixed>
     */
    protected function getInternalParameters(): array
    {
        return [
            'customer_group' => $this->customerGroup,
            'form' => $this->form->createView(),
        ];
    }
}
