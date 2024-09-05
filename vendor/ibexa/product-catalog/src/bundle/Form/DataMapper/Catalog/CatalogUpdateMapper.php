<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataMapper\Catalog;

use Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\CatalogUpdateData;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\MatchAll;

final class CatalogUpdateMapper
{
    public function mapToStruct(CatalogUpdateData $data): CatalogUpdateStruct
    {
        $languageCode = $data->getLanguage()->languageCode;

        return new CatalogUpdateStruct(
            $data->getId(),
            null,
            $data->getIdentifier(),
            $data->getCriteria() ?? new LogicalAnd([new MatchAll()]),
            [
                $languageCode => $data->getName(),
            ],
            [
                $languageCode => $data->getDescription() ?? '',
            ],
        );
    }
}
