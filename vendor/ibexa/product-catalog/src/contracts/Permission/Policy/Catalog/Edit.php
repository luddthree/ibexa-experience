<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Permission\Policy\Catalog;

use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogUpdateStruct;

final class Edit extends AbstractCatalogPolicy
{
    private const EDIT = 'edit';

    private ?CatalogUpdateStruct $catalogUpdateStruct;

    public function __construct(?CatalogUpdateStruct $catalogUpdateStruct = null)
    {
        $this->catalogUpdateStruct = $catalogUpdateStruct;
    }

    public function getFunction(): string
    {
        return self::EDIT;
    }

    public function getObject(): ?CatalogUpdateStruct
    {
        return $this->catalogUpdateStruct;
    }
}
