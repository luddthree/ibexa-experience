<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Catalog;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;

interface CatalogQuerySerializerInterface
{
    public function serialize(CriterionInterface $criteria): string;

    public function deserialize(string $queryString): CriterionInterface;
}
