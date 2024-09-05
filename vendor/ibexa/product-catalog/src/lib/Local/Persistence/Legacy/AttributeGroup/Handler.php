<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeGroup;

use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeGroup\Gateway\Translation\GatewayInterface as TranslationGatewayInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeGroup;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeGroupCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeGroupUpdateStruct;

final class Handler implements HandlerInterface
{
    private GatewayInterface $gateway;

    private TranslationGatewayInterface $translationGateway;

    private Mapper $mapper;

    public function __construct(GatewayInterface $gateway, TranslationGatewayInterface $translationGateway, Mapper $mapper)
    {
        $this->gateway = $gateway;
        $this->translationGateway = $translationGateway;
        $this->mapper = $mapper;
    }

    public function load(int $id): AttributeGroup
    {
        $row = $this->gateway->findById($id);
        if ($row === null) {
            throw new NotFoundException(AttributeGroup::class, $id);
        }

        $translations = $this->translationGateway->findByAttributeGroupId((int)$row['id']);

        return $this->mapper->extractFromRow($row, $translations);
    }

    public function loadByIdentifier(string $identifier): AttributeGroup
    {
        $row = $this->gateway->findByIdentifier($identifier);
        if ($row === null) {
            throw new NotFoundException(AttributeGroup::class, $identifier);
        }

        $translations = $this->translationGateway->findByAttributeGroupId((int)$row['id']);

        return $this->mapper->extractFromRow($row, $translations);
    }

    public function findMatching(?string $namePrefix, int $offset, int $limit): array
    {
        $rows = $this->gateway->findMatching($namePrefix, $offset, $limit);

        $translations = $this->translationGateway->findByAttributeGroupIds(
            array_column($rows, 'id')
        );

        return $this->mapper->extractFromRows($rows, $translations);
    }

    public function countMatching(?string $namePrefix): int
    {
        return $this->gateway->countMatching($namePrefix);
    }

    public function create(AttributeGroupCreateStruct $createStruct): void
    {
        $this->gateway->insert($createStruct);
    }

    public function update(AttributeGroupUpdateStruct $updateStruct): void
    {
        $this->gateway->update($updateStruct);
    }

    public function deleteByIdentifier(string $identifier): void
    {
        $this->gateway->deleteByIdentifier($identifier);
    }

    public function deleteTranslation(string $identifier, int $languageId): void
    {
        $attributeGroup = $this->loadByIdentifier($identifier);

        $this->translationGateway->delete($attributeGroup->id, $languageId);
    }
}
