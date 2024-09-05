<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ActivityLog\Values\ActivityLog\SortClause;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;

abstract class AbstractSortClause implements SortClauseInterface
{
    private const ALLOWED_SORT = [
        self::ASC,
        self::DESC,
    ];

    /**
     * @phpstan-var self::ASC|self::DESC
     */
    private string $order;

    /**
     * @phpstan-param self::ASC|self::DESC $order
     *
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    public function __construct(string $order)
    {
        $this->setOrder($order);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    public function setOrder(string $order): void
    {
        if (!in_array($order, self::ALLOWED_SORT, true)) {
            throw new InvalidArgumentException(
                '$order',
                sprintf(
                    'Expected one of: "%s". Received "%s".',
                    implode('", "', self::ALLOWED_SORT),
                    $order,
                ),
            );
        }

        $this->order = $order;
    }

    public function getOrder(): string
    {
        return $this->order;
    }
}
