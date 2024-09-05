<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Permission\Policy\Catalog;

use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;

final class View extends AbstractCatalogPolicy
{
    private const VIEW = 'view';

    private ?CatalogInterface $catalog;

    public function __construct(?CatalogInterface $catalog = null)
    {
        $this->catalog = $catalog;
    }

    public function getFunction(): string
    {
        return self::VIEW;
    }

    public function getObject(): ?CatalogInterface
    {
        return $this->catalog;
    }
}
