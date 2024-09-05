<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogListInterface;
use Ibexa\Rest\Value;

final class CatalogView extends Value
{
    private string $identifier;

    private CatalogListInterface $catalogList;

    public function __construct(string $identifier, CatalogListInterface $catalogList)
    {
        $this->identifier = $identifier;
        $this->catalogList = $catalogList;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getCatalogList(): CatalogListInterface
    {
        return $this->catalogList;
    }
}
