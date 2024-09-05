<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Permission\Policy\Catalog;

use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogCreateStruct;

final class Create extends AbstractCatalogPolicy
{
    private const CREATE = 'create';

    private ?CatalogCreateStruct $catalogCreateStruct;

    public function __construct(?CatalogCreateStruct $catalogCreateStruct = null)
    {
        $this->catalogCreateStruct = $catalogCreateStruct;
    }

    public function getFunction(): string
    {
        return self::CREATE;
    }

    public function getObject(): ?CatalogCreateStruct
    {
        return $this->catalogCreateStruct;
    }
}
