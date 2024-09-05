<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Catalog;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductCode;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductType;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;

final class CatalogQuerySerializer implements CatalogQuerySerializerInterface
{
    public function serialize(CriterionInterface $criteria): string
    {
        return str_replace(
            "\0",
            '~~NULL_BYTE~~',
            serialize($criteria)
        );
    }

    public function deserialize(string $queryString): CriterionInterface
    {
        $serialized = str_replace(
            '~~NULL_BYTE~~',
            "\0",
            $queryString
        );

        /** @var \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalAnd $query */
        $query = unserialize($serialized, [LogicalAnd::class, ProductCode::class, ProductType::class]);

        return $query;
    }
}
