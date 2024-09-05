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

interface CustomerGroupServiceInterface
{
    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function createCustomerGroup(CustomerGroupCreateStruct $createStruct): CustomerGroupInterface;

    public function deleteCustomerGroup(CustomerGroupInterface $customerGroup): void;

    public function deleteCustomerGroupTranslation(CustomerGroupDeleteTranslationStruct $struct): void;

    /**
     * @param string[]|null $prioritizedLanguages
     */
    public function getCustomerGroupByIdentifier(string $identifier, ?array $prioritizedLanguages = null): ?CustomerGroupInterface;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function findCustomerGroups(?CustomerGroupQuery $query = null): CustomerGroupListInterface;

    /**
     * @param string[]|null $prioritizedLanguages
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function getCustomerGroup(int $id, ?array $prioritizedLanguages = null): CustomerGroupInterface;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function updateCustomerGroup(CustomerGroupUpdateStruct $updateStruct): CustomerGroupInterface;
}
