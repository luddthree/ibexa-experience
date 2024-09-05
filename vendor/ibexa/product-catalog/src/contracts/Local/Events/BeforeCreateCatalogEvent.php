<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Events;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use UnexpectedValueException;

final class BeforeCreateCatalogEvent extends BeforeEvent
{
    private CatalogCreateStruct $createStruct;

    private ?CatalogInterface $catalog = null;

    public function __construct(CatalogCreateStruct $createStruct)
    {
        $this->createStruct = $createStruct;
    }

    public function getCreateStruct(): CatalogCreateStruct
    {
        return $this->createStruct;
    }

    public function getResultCatalog(): CatalogInterface
    {
        if ($this->catalog === null) {
            $message = 'Return value is not set or not of type %s. Check hasResultCatalog() or'
                . ' set it using setResultCatalog() before you call the getter.';

            throw new UnexpectedValueException(sprintf($message, CatalogInterface::class));
        }

        return $this->catalog;
    }

    public function hasResultCatalog(): bool
    {
        return $this->catalog instanceof CatalogInterface;
    }

    public function setCatalog(?CatalogInterface $catalog): void
    {
        $this->catalog = $catalog;
    }
}
