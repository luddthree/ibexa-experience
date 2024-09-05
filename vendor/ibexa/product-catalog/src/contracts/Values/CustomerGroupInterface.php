<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values;

interface CustomerGroupInterface
{
    public function getId(): int;

    public function getName(): string;

    public function getIdentifier(): string;

    public function getDescription(): string;

    /**
     * A list of users attached to the Customer Group.
     *
     * @phpstan-return iterable<\Ibexa\Contracts\Core\Repository\Values\User\User>
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\User[]
     */
    public function getUsers(): iterable;

    /**
     * @return numeric-string
     */
    public function getGlobalPriceRate(): string;
}
