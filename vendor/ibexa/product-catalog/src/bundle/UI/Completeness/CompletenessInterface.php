<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI\Completeness;

use Ibexa\Contracts\ProductCatalog\Values\Common\Percent;
use Iterator;
use IteratorAggregate;

/**
 * @extends IteratorAggregate<\Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\TaskInterface>
 */
interface CompletenessInterface extends IteratorAggregate
{
    public function getValue(): Percent;

    public function isComplete(): bool;

    public function getIterator(): Iterator;
}
