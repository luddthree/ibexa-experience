<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataMapper\Catalog;

use Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\CatalogCreateData;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\MatchAll;

final class CatalogCreateMapper
{
    public function mapToStruct(CatalogCreateData $data): CatalogCreateStruct
    {
        $languageCode = $data->getLanguage()->languageCode;

        return new CatalogCreateStruct(
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
