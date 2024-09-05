<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\FieldType\CustomerGroup;

use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Core\FieldType\Value as CoreValue;

final class Value extends CoreValue
{
    public ?int $id;

    private ?CustomerGroupInterface $customerGroup;

    public function __construct(
        ?int $id,
        ?CustomerGroupInterface $customerGroup = null
    ) {
        parent::__construct();

        $this->id = $id;
        $this->customerGroup = $customerGroup;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomerGroup(): ?CustomerGroupInterface
    {
        return $this->customerGroup;
    }

    public function __toString(): string
    {
        return (string) ($this->id ?? '');
    }
}
