<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI\Completeness\Task;

use Ibexa\Bundle\ProductCatalog\UI\Completeness\Entry\EntryInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

/**
 * @internal
 */
interface TaskInterface
{
    public function getIdentifier(): string;

    public function getName(): string;

    public function getEntry(ProductInterface $product): ?EntryInterface;

    /**
     * @return array<\Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\TaskGroup>|null
     */
    public function getSubtaskGroups(ProductInterface $product): ?array;

    /**
     * @phpstan-return int<1, max>
     */
    public function getWeight(): int;

    public function getEditUrl(ProductInterface $product): ?string;
}
