<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Common\Query;

final class SortDirection
{
    public const ASC = 'ascending';
    public const DESC = 'descending';

    /**
     * @phpstan-assert-if-true self::ASC|self::DESC $value
     */
    public static function isValid(string $value): bool
    {
        return $value === self::ASC || $value === self::DESC;
    }
}
