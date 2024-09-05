<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local;

use Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeGroup\AttributeGroupCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeGroup\AttributeGroupUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;

interface LocalAttributeGroupServiceInterface extends AttributeGroupServiceInterface
{
    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function createAttributeGroup(AttributeGroupCreateStruct $createStruct): AttributeGroupInterface;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function deleteAttributeGroup(AttributeGroupInterface $group): void;

    public function newAttributeGroupCreateStruct(string $identifier): AttributeGroupCreateStruct;

    public function newAttributeGroupUpdateStruct(AttributeGroupInterface $group): AttributeGroupUpdateStruct;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function updateAttributeGroup(
        AttributeGroupInterface $attributeGroup,
        AttributeGroupUpdateStruct $updateStruct
    ): AttributeGroupInterface;

    public function deleteAttributeGroupTranslation(
        AttributeGroupInterface $attributeGroup,
        string $languageCode
    ): void;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function isAttributeGroupUsed(
        AttributeGroupInterface $attributeGroup
    ): bool;
}
