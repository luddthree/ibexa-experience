<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataMapper\Catalog;

use Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\CatalogCopyData;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogCopyStruct;

final class CatalogCopyMapper
{
    public function mapToStruct(CatalogCopyData $data): CatalogCopyStruct
    {
        return new CatalogCopyStruct(
            $data->getCatalog()->getId(),
            $data->getIdentifier()
        );
    }
}
