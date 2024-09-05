<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Permission\Policy\Commerce;

final class AdministrateRegions extends AbstractCommercePolicy
{
    private const REGION = 'region';

    public function getFunction(): string
    {
        return self::REGION;
    }

    public function getObject(): ?object
    {
        return null;
    }
}
