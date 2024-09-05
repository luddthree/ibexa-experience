<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Permission\Policy\CustomerGroup;

use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupCreateStruct;

final class Create extends AbstractCustomerGroupPolicy
{
    private const CREATE = 'create';

    private ?CustomerGroupCreateStruct $customerGroupCreateStruct;

    public function __construct(?CustomerGroupCreateStruct $customerGroupCreateStruct = null)
    {
        $this->customerGroupCreateStruct = $customerGroupCreateStruct;
    }

    public function getFunction(): string
    {
        return self::CREATE;
    }

    public function getObject(): ?CustomerGroupCreateStruct
    {
        return $this->customerGroupCreateStruct;
    }
}
