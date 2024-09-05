<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Catalog;

use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\Catalog as SpiCatalog;

interface DomainMapperInterface
{
    /**
     * @phpstan-param iterable<\Ibexa\Contracts\Core\Persistence\Content\Language>|null $prioritizedLanguages
     */
    public function createFromSpi(
        SpiCatalog $spiCatalog,
        User $user,
        ?iterable $prioritizedLanguages = null
    ): CatalogInterface;
}
