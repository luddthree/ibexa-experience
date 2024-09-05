<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Events;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use UnexpectedValueException;

final class BeforeUpdateCatalogEvent extends BeforeEvent
{
    private CatalogInterface $catalog;

    private CatalogUpdateStruct $updateStruct;

    private ?CatalogInterface $resultCatalog = null;

    public function __construct(
        CatalogInterface $catalog,
        CatalogUpdateStruct $updateStruct
    ) {
        $this->catalog = $catalog;
        $this->updateStruct = $updateStruct;
    }

    public function getCatalog(): CatalogInterface
    {
        return $this->catalog;
    }

    public function setCatalog(CatalogInterface $catalog): void
    {
        $this->catalog = $catalog;
    }

    public function getUpdateStruct(): CatalogUpdateStruct
    {
        return $this->updateStruct;
    }

    public function setUpdateStruct(CatalogUpdateStruct $updateStruct): void
    {
        $this->updateStruct = $updateStruct;
    }

    public function getResultCatalog(): CatalogInterface
    {
        if ($this->resultCatalog === null) {
            $message = 'Return value is not set or not of type %s. Check hasResultCatalog() or'
                . ' set it using setResultCatalog() before you call the getter.';

            throw new UnexpectedValueException(sprintf($message, CatalogInterface::class));
        }

        return $this->resultCatalog;
    }

    public function hasResultCatalog(): bool
    {
        return $this->resultCatalog instanceof CatalogInterface;
    }

    public function setResultCatalog(?CatalogInterface $resultCatalog): void
    {
        $this->resultCatalog = $resultCatalog;
    }
}
