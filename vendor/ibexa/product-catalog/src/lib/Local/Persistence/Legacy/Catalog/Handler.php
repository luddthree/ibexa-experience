<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Catalog;

use Ibexa\Contracts\Core\Persistence\Content\Language\Handler as LanguageHandler;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\ProductCatalog\Exception\BadStateException;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Catalog\Gateway\Translation\GatewayInterface as TranslationGatewayInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\Catalog;
use Ibexa\ProductCatalog\Local\Persistence\Values\CatalogCopyStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\CatalogCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\CatalogUpdateStruct;

final class Handler implements HandlerInterface
{
    private GatewayInterface $gateway;

    private Mapper $mapper;

    private TranslationGatewayInterface $translationGateway;

    private LanguageHandler $languageHandler;

    public function __construct(
        GatewayInterface $gateway,
        TranslationGatewayInterface $translationGateway,
        Mapper $mapper,
        LanguageHandler $languageHandler
    ) {
        $this->gateway = $gateway;
        $this->mapper = $mapper;
        $this->translationGateway = $translationGateway;
        $this->languageHandler = $languageHandler;
    }

    public function create(CatalogCreateStruct $createStruct): int
    {
        return $this->gateway->insert($createStruct);
    }

    public function update(CatalogUpdateStruct $updateStruct): void
    {
        $this->gateway->update($updateStruct);
    }

    public function delete(int $id): void
    {
        $this->gateway->delete($id);
    }

    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, int $offset = 0): array
    {
        $rows = $this->gateway->findBy($criteria, $orderBy, $limit, $offset);

        $translations = $this->translationGateway->findByTranslatableIds(
            array_column($rows, 'id'),
        );

        return $this->mapper->createFromRows($rows, $translations);
    }

    public function findAll(?int $limit = null, int $offset = 0): array
    {
        $rows = $this->gateway->findAll($limit, $offset);

        $translations = $this->translationGateway->findByTranslatableIds(
            array_column($rows, 'id'),
        );

        return $this->mapper->createFromRows($rows, $translations);
    }

    public function countAll(): int
    {
        return $this->gateway->countAll();
    }

    public function countBy(array $criteria): int
    {
        return $this->gateway->countBy($criteria);
    }

    public function find(int $id): Catalog
    {
        $row = $this->gateway->findById($id);

        if ($row === null) {
            throw new NotFoundException(CatalogInterface::class, $id);
        }

        $translations = $this->translationGateway->findByTranslatableId($row['id']);

        return $this->mapper->createFromRow($row, $translations);
    }

    public function copy(CatalogCopyStruct $copyStruct): int
    {
        return $this->gateway->copy($copyStruct);
    }

    public function exists(int $id): bool
    {
        $row = $this->gateway->findById($id);

        return $row !== null;
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?Catalog
    {
        $rows = $this->gateway->findBy($criteria, $orderBy, 1);

        if (!isset($rows[0])) {
            return null;
        }
        $row = $rows[0];
        $translations = $this->translationGateway->findByTranslatableId($row['id']);

        return $this->mapper->createFromRow($row, $translations);
    }

    public function deleteTranslation(CatalogInterface $catalog, string $languageCode): void
    {
        $language = $this->languageHandler->loadByLanguageCode($languageCode);

        $translationsRemaining = $this->translationGateway->countByTranslatableId($catalog->getId());
        if ($translationsRemaining === 1) {
            throw new BadStateException(
                '$catalog',
                'Cannot remove Catalog translation. No translations would remain after this operation',
            );
        }

        $this->translationGateway->deleteBy([
            'catalog_id' => $catalog->getId(),
            'language_id' => $language->id,
        ]);
    }
}
