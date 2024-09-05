<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Events;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;

final class BeforeDeleteCatalogEvent extends BeforeEvent
{
    private CatalogInterface $catalog;

    public function __construct(CatalogInterface $catalog)
    {
        $this->catalog = $catalog;
    }

    public function getCatalog(): CatalogInterface
    {
        return $this->catalog;
    }
}
