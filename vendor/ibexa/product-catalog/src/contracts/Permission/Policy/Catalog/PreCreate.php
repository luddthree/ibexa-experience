<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Permission\Policy\Catalog;

use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;

final class PreCreate extends AbstractCatalogPolicy
{
    private const CREATE = 'create';

    private ?CatalogInterface $catalog;

    public function __construct(?CatalogInterface $catalog = null)
    {
        $this->catalog = $catalog;
    }

    public function getFunction(): string
    {
        return self::CREATE;
    }

    public function getObject(): ?CatalogInterface
    {
        return $this->catalog;
    }
}
