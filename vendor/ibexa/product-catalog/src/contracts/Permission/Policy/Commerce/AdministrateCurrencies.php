<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Permission\Policy\Commerce;

final class AdministrateCurrencies extends AbstractCommercePolicy
{
    private const CURRENCY = 'currency';

    public function getFunction(): string
    {
        return self::CURRENCY;
    }

    public function getObject(): ?object
    {
        return null;
    }
}
