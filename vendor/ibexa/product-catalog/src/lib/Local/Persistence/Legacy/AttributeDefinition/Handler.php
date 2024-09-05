<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinition;

use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionQuery;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinition\Gateway\Schema;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinition\Gateway\Translation\GatewayInterface as TranslationGatewayInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinition;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinitionCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinitionUpdateStruct;
use Ibexa\ProductCatalog\Local\Repository\CriterionMapper;

final class Handler implements HandlerInterface
{
    private GatewayInterface $gateway;

    private TranslationGatewayInterface $translationGateway;

    private Mapper $mapper;

    private CriterionMapper $criterionMapper;

    public function __construct(
        GatewayInterface $gateway,
        TranslationGatewayInterface $translationGateway,
        Mapper $mapper,
        CriterionMapper $criterionMapper
    ) {
        $this->gateway = $gateway;
        $this->translationGateway = $translationGateway;
        $this->mapper = $mapper;
        $this->criterionMapper = $criterionMapper;
    }

    public function load(int $id): AttributeDefinition
    {
        $row = $this->gateway->findById($id);
        if ($row === null) {
            throw new NotFoundException(AttributeDefinition::class, $id);
        }

        $translations = $this->translationGateway->findByTranslatableId((int)$row['id']);

        return $this->mapper->extractFromRow($row, $translations);
    }

    public function loadByIdentifier(string $identifier): AttributeDefinition
    {
        $row = $this->gateway->findByIdentifier($identifier);
        if ($row === null) {
            throw new NotFoundException(AttributeDefinition::class, $identifier);
        }

        $translations = $this->translationGateway->findByTranslatableId((int)$row['id']);

        return $this->mapper->extractFromRow($row, $translations);
    }

    public function findMatching(AttributeDefinitionQuery $query): array
    {
        $offset = $query->getOffset();
        $limit = $query->getLimit();

        $criteria = [];
        if ($query->getQuery() !== null) {
            $criteria = $this->criterionMapper->handle($query->getQuery());
        }

        $rows = $this->gateway->findBy(
            $criteria,
            [
                Schema::COLUMN_POSITION => 'ASC',
                Schema::COLUMN_IDENTIFIER => 'ASC',
            ],
            $limit,
            $offset
        );

        $translations = $this->translationGateway->findByTranslatableIds(array_column($rows, 'id'));

        return $this->mapper->extractFromRows($rows, $translations);
    }

    public function countMatching(AttributeDefinitionQuery $query): int
    {
        if ($query->getQuery() !== null) {
            $criteria = $this->criterionMapper->handle($query->getQuery());

            return $this->gateway->countBy($criteria);
        }

        return $this->gateway->countAll();
    }

    public function create(AttributeDefinitionCreateStruct $createStruct): void
    {
        $this->gateway->insert($createStruct);
    }

    public function update(AttributeDefinitionUpdateStruct $updateStruct): void
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
