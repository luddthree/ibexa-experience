<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Converter;

use Ibexa\Bundle\Core\Converter\RepositoryParamConverter;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;

final class CustomerGroupParamConverter extends RepositoryParamConverter
{
    private CustomerGroupServiceInterface $customerGroupService;

    public function __construct(CustomerGroupServiceInterface $customerGroupService)
    {
        $this->customerGroupService = $customerGroupService;
    }

    /** @phpstan-return class-string */
    protected function getSupportedClass()
    {
        return CustomerGroupInterface::class;
    }

    /**
     * @return string
     */
    protected function getPropertyName()
    {
        return 'customerGroupId';
    }

    /**
     * @param string $id
     */
    protected function loadValueObject($id): CustomerGroupInterface
    {
        return $this->customerGroupService->getCustomerGroup((int) $id);
    }
}
