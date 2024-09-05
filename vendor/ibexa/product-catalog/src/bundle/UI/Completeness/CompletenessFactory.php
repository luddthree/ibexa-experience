<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI\Completeness;

use Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\TaskRegistry;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

final class CompletenessFactory implements CompletenessFactoryInterface
{
    private TaskRegistry $taskRegistry;

    public function __construct(TaskRegistry $taskRegistry)
    {
        $this->taskRegistry = $taskRegistry;
    }

    public function createProductCompleteness(ProductInterface $product): CompletenessInterface
    {
        $tasks = [];
        foreach ($this->taskRegistry->getTasks() as $task) {
            $tasks[] = $task;
        }

        return new Completeness($product, $tasks);
    }
}
