<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Events;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;

final class UpdateCatalogEvent extends AfterEvent
{
    private CatalogInterface $catalog;

    private CatalogUpdateStruct $updateStruct;

    public function __construct(
        CatalogInterface $catalog,
        CatalogUpdateStruct $updateStruct
    ) {
        $this->updateStruct = $updateStruct;
        $this->catalog = $catalog;
    }

    public function getCatalog(): CatalogInterface
    {
        return $this->catalog;
    }

    public function getUpdateStruct(): CatalogUpdateStruct
    {
        return $this->updateStruct;
    }
}
