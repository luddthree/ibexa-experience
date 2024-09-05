<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI\Completeness\Task;

final class TaskRegistry
{
    /**
     * @var \Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\TaskInterface[]
     */
    private iterable $tasks;

    /**
     * @param iterable<\Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\TaskInterface> $tasks
     */
    public function __construct(iterable $tasks)
    {
        $this->tasks = $tasks;
    }

    /**
     * @return iterable<\Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\TaskInterface>
     */
    public function getTasks(): iterable
    {
        return $this->tasks;
    }
}
