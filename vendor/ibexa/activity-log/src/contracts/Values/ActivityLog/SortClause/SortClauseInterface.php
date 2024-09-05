<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ActivityLog\Values\ActivityLog\SortClause;

interface SortClauseInterface
{
    public const DESC = 'DESC';

    public const ASC = 'ASC';

    /**
     * @phpstan-param self::ASC|self::DESC $order
     *
     * @throws \InvalidArgumentException if $order param is not "ASC" or "DESC"
     */
    public function setOrder(string $order): void;

    /**
     * @phpstan-return self::ASC|self::DESC
     */
    public function getOrder(): string;
}
