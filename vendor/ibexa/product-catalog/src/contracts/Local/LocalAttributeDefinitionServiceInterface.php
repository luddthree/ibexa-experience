<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local;

use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeDefinition\AttributeDefinitionCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeDefinition\AttributeDefinitionUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;

interface LocalAttributeDefinitionServiceInterface extends AttributeDefinitionServiceInterface
{
    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function createAttributeDefinition(
        AttributeDefinitionCreateStruct $createStruct
    ): AttributeDefinitionInterface;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function deleteAttributeDefinition(
        AttributeDefinitionInterface $attributeDefinition
    ): void;

    public function newAttributeDefinitionCreateStruct(
        string $identifier
    ): AttributeDefinitionCreateStruct;

    public function newAttributeDefinitionUpdateStruct(
        AttributeDefinitionInterface $attributeDefinition
    ): AttributeDefinitionUpdateStruct;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function updateAttributeDefinition(
        AttributeDefinitionInterface $attributeDefinition,
        AttributeDefinitionUpdateStruct $updateStruct
    ): AttributeDefinitionInterface;

    public function deleteAttributeDefinitionTranslation(
        AttributeDefinitionInterface $attributeDefinition,
        string $languageCode
    ): void;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function isAttributeDefinitionUsed(
        AttributeDefinitionInterface $attributeDefinition
    ): bool;
}
