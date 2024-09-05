<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Permission\Policy\CustomerGroup;

use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupUpdateStruct;

final class Edit extends AbstractCustomerGroupPolicy
{
    private const EDIT = 'edit';

    private ?CustomerGroupUpdateStruct $customerGroupUpdateStruct;

    public function __construct(?CustomerGroupUpdateStruct $customerGroupUpdateStruct = null)
    {
        $this->customerGroupUpdateStruct = $customerGroupUpdateStruct;
    }

    public function getFunction(): string
    {
        return self::EDIT;
    }

    public function getObject(): ?CustomerGroupUpdateStruct
    {
        return $this->customerGroupUpdateStruct;
    }
}
