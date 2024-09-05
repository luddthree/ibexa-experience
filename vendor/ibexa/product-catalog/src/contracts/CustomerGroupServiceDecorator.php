<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog;

use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupDeleteTranslationStruct;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupListInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupQuery;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;

abstract class CustomerGroupServiceDecorator implements CustomerGroupServiceInterface
{
    protected CustomerGroupServiceInterface $innerService;

    public function __construct(CustomerGroupServiceInterface $innerService)
    {
        $this->innerService = $innerService;
    }

    public function createCustomerGroup(CustomerGroupCreateStruct $createStruct): CustomerGroupInterface
    {
        return $this->innerService->createCustomerGroup($createStruct);
    }

    public function deleteCustomerGroup(CustomerGroupInterface $customerGroup): void
    {
        $this->innerService->deleteCustomerGroup($customerGroup);
    }

    public function deleteCustomerGroupTranslation(CustomerGroupDeleteTranslationStruct $struct): void
    {
        $this->innerService->deleteCustomerGroupTranslation($struct);
    }

    public function getCustomerGroupByIdentifier(string $identifier, ?array $prioritizedLanguages = null): ?CustomerGroupInterface
    {
        return $this->innerService->getCustomerGroupByIdentifier($identifier, $prioritizedLanguages);
    }

    public function findCustomerGroups(?CustomerGroupQuery $query = null): CustomerGroupListInterface
    {
        return $this->innerService->findCustomerGroups($query);
    }

    public function getCustomerGroup(int $id, ?array $prioritizedLanguages = null): CustomerGroupInterface
    {
        return $this->innerService->getCustomerGroup($id, $prioritizedLanguages);
    }

    public function updateCustomerGroup(CustomerGroupUpdateStruct $updateStruct): CustomerGroupInterface
    {
        return $this->innerService->updateCustomerGroup($updateStruct);
    }
}
