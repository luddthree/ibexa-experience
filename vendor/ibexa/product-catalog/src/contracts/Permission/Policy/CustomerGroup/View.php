<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Permission\Policy\CustomerGroup;

use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;

final class View extends AbstractCustomerGroupPolicy
{
    private const VIEW = 'view';

    private ?CustomerGroupInterface $customerGroup;

    public function __construct(?CustomerGroupInterface $customerGroup = null)
    {
        $this->customerGroup = $customerGroup;
    }

    public function getFunction(): string
    {
        return self::VIEW;
    }

    public function getObject(): ?CustomerGroupInterface
    {
        return $this->customerGroup;
    }
}
