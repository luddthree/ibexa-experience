<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinitionAssignment;

use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinitionAssignmentCreateStruct;

final class Handler implements HandlerInterface
{
    private GatewayInterface $gateway;

    private Mapper $mapper;

    public function __construct(GatewayInterface $gateway, Mapper $mapper)
    {
        $this->gateway = $gateway;
        $this->mapper = $mapper;
    }

    public function create(AttributeDefinitionAssignmentCreateStruct $createStruct, int $status): void
    {
        $this->gateway->insert($createStruct, $status);
    }

    public function publish(int $fieldDefinitionId): void
    {
        $this->gateway->publish($fieldDefinitionId);
    }

    public function deleteByFieldDefinitionId(int $fieldDefinitionId, int $status): void
    {
        $this->gateway->deleteByFieldDefinitionId($fieldDefinitionId, $status);
    }

    public function findByFieldDefinitionId(int $fieldDefinitionId, int $status): array
    {
        return $this->mapper->extractFromRows(
            $this->gateway->findByFieldDefinitionId($fieldDefinitionId, $status)
        );
    }

    public function getIdentityMap(int $fieldDefinitionId, bool $discriminator = false): array
    {
        return $this->gateway->getIdentityMap($fieldDefinitionId, $discriminator);
    }
}
