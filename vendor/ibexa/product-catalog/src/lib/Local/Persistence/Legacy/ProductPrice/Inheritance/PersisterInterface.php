<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\Inheritance;

use Doctrine\DBAL\Connection;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadataInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\ProductPriceCreateStructInterface;

/**
 * Responsible for persisting Class-Table inherited data.
 */
interface PersisterInterface
{
    public function canHandle(ProductPriceCreateStructInterface $struct): bool;

    public function handleProductPriceCreate(
        int $id,
        Connection $connection,
        DoctrineSchemaMetadataInterface $metadata,
        ProductPriceCreateStructInterface $struct
    ): void;

    public function addInheritanceMetadata(DoctrineSchemaMetadataInterface $metadata): void;
}
