<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\View;

use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;

final class IndividualListViewEntry
{
    private User $user;

    private ?CustomerGroupInterface $customerGroup;

    public function __construct(User $user, ?CustomerGroupInterface $customerGroup)
    {
        $this->user = $user;
        $this->customerGroup = $customerGroup;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function hasCustomerGroup(): bool
    {
        return $this->customerGroup !== null;
    }

    public function getCustomerGroup(): ?CustomerGroupInterface
    {
        return $this->customerGroup;
    }
}
