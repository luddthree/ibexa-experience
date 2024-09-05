<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\View;

use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Core\MVC\Symfony\View\BaseView;

final class CustomerGroupDetailedView extends BaseView
{
    private CustomerGroupInterface $customerGroup;

    /** @var iterable<\Ibexa\Contracts\Core\Repository\Values\User\User> */
    private iterable $users;

    /**
     * @param iterable<\Ibexa\Contracts\Core\Repository\Values\User\User> $users
     */
    public function __construct(
        string $templateIdentifier,
        CustomerGroupInterface $customerGroup,
        iterable $users = []
    ) {
        parent::__construct($templateIdentifier);

        $this->customerGroup = $customerGroup;
        $this->users = $users;
    }

    public function getCustomerGroup(): CustomerGroupInterface
    {
        return $this->customerGroup;
    }

    public function setCustomerGroup(CustomerGroupInterface $customerGroup): void
    {
        $this->customerGroup = $customerGroup;
    }

    /**
     * @return iterable<\Ibexa\Contracts\Core\Repository\Values\User\User>
     */
    public function getUsers(): iterable
    {
        return $this->users;
    }

    /**
     * @param iterable<\Ibexa\Contracts\Core\Repository\Values\User\User> $users
     */
    public function setUsers(iterable $users): void
    {
        $this->users = $users;
    }

    /**
     * @return array<string,mixed>
     */
    protected function getInternalParameters(): array
    {
        return [
            'customer_group' => $this->customerGroup,
            'users' => $this->getUsers(),
        ];
    }
}
