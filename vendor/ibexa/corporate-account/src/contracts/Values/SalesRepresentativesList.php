<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Values;

use ArrayIterator;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use IteratorAggregate;

final class SalesRepresentativesList implements IteratorAggregate
{
    /** @var array<\Ibexa\Contracts\Core\Repository\Values\User\User> */
    private array $salesRepresentatives;

    /**
     * @param array<\Ibexa\Contracts\Core\Repository\Values\User\User> $salesRepresentatives
     */
    public function __construct(array $salesRepresentatives = [])
    {
        $this->salesRepresentatives = array_values($salesRepresentatives);
    }

    public function append(User $user): void
    {
        $this->salesRepresentatives[] = $user;
    }

    /**
     * @return \ArrayIterator<\Ibexa\Contracts\Core\Repository\Values\User\User>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->salesRepresentatives);
    }
}
