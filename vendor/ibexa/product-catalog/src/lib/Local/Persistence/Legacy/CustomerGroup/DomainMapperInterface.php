<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup;

use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\CustomerGroup as SpiCustomerGroup;

interface DomainMapperInterface
{
    /**
     * @phpstan-param iterable<\Ibexa\Contracts\Core\Persistence\Content\Language>|null $prioritizedLanguages
     */
    public function createFromSpi(
        SpiCustomerGroup $spiCustomerGroup,
        ?iterable $prioritizedLanguages = null
    ): CustomerGroupInterface;
}
