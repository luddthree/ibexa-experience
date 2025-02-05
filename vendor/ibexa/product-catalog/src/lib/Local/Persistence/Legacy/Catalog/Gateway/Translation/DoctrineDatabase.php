<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Catalog\Gateway\Translation;

use Doctrine\DBAL\Types\Types;
use Ibexa\Contracts\CorePersistence\Gateway\AbstractTranslationGateway;
use Ibexa\Contracts\CorePersistence\Gateway\TranslationDoctrineSchemaMetadata;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;

/**
 * @extends \Ibexa\Contracts\CorePersistence\Gateway\AbstractTranslationGateway<
 *     array{
 *         id: int,
 *         catalog_id: int,
 *         language_id: int,
 *         name: string,
 *         description: string,
 *     },
 * >
 */
final class DoctrineDatabase extends AbstractTranslationGateway implements GatewayInterface
{
    protected function getTableName(): string
    {
        return Schema::TABLE_NAME;
    }

    protected function buildMetadata(): TranslationDoctrineSchemaMetadata
    {
        return new TranslationDoctrineSchemaMetadata(
            $this->connection,
            CatalogInterface::class,
            $this->getTableName(),
            [
                Schema::COLUMN_ID => Types::INTEGER,
                Schema::COLUMN_CATALOG_ID => Types::INTEGER,
                Schema::COLUMN_LANGUAGE_ID => Types::INTEGER,
                Schema::COLUMN_NAME => Types::STRING,
                Schema::COLUMN_NAME_NORMALIZED => Types::STRING,
                Schema::COLUMN_DESCRIPTION => Types::STRING,
            ],
            [Schema::COLUMN_ID],
            Schema::COLUMN_LANGUAGE_ID,
            Schema::COLUMN_CATALOG_ID,
        );
    }

    public function deleteBy(array $criteria): void
    {
        $this->doDelete($criteria);
    }
}
