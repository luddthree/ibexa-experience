<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Events;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;

final class CreateCatalogEvent extends AfterEvent
{
    private CatalogCreateStruct $createStruct;

    private CatalogInterface $catalog;

    public function __construct(
        CatalogCreateStruct $createStruct,
        CatalogInterface $catalog
    ) {
        $this->createStruct = $createStruct;
        $this->catalog = $catalog;
    }

    public function getCreateStruct(): CatalogCreateStruct
    {
        return $this->createStruct;
    }

    public function getCatalog(): CatalogInterface
    {
        return $this->catalog;
    }
}
