<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Value;

use ArrayIterator;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use IteratorAggregate;
use Traversable;

final class WorkflowMetadataList extends ValueObject implements IteratorAggregate
{
    /** @var int|null */
    public $totalCount = null;

    /** @var \Ibexa\Workflow\Value\WorkflowMetadata[] */
    public $items = [];

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }
}
