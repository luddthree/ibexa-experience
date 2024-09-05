<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI\Completeness\Task;

use Ibexa\Contracts\ProductCatalog\Values\Common\Percent;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

/**
 * @internal
 */
final class TaskGroup
{
    private string $identifier;

    private string $name;

    private ProductInterface $product;

    /** @var \Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\TaskInterface[] */
    private array $tasks;

    /** @var \Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\TaskInterface[]|null */
    private ?array $incompleteTasks = null;

    /**
     * @param \Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\TaskInterface[] $tasks
     */
    public function __construct(
        string $identifier,
        string $name,
        ProductInterface $product,
        array $tasks = []
    ) {
        $this->identifier = $identifier;
        $this->name = $name;
        $this->product = $product;
        $this->tasks = $tasks;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getProduct(): ProductInterface
    {
        return $this->product;
    }

    /**
     * @param \Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\TaskInterface[] $tasks
     */
    public function setTasks(array $tasks): void
    {
        $this->tasks = $tasks;
        $this->incompleteTasks = null;
    }

    public function addTask(TaskInterface $task): void
    {
        $this->tasks[] = $task;
        $this->incompleteTasks = null;
    }

    /**
     * @return \Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\TaskInterface[]
     */
    public function getTasks(): array
    {
        return $this->tasks;
    }

    /**
     * @return \Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\TaskInterface[]
     */
    public function getIncompleteTasks(): array
    {
        if ($this->incompleteTasks !== null) {
            return $this->incompleteTasks;
        }

        foreach ($this->tasks as $task) {
            $entry = $task->getEntry($this->product);
            if ($entry === null || $entry->isComplete()) {
                continue;
            }

            $this->incompleteTasks[] = $task;
        }

        return $this->incompleteTasks ?? [];
    }

    public function getCompleteness(): Percent
    {
        $completenessValue = 0.0;
        $tasksCount = count($this->tasks);

        if ($tasksCount === 0) {
            return Percent::zero();
        }

        foreach ($this->tasks as $task) {
            $entry = $task->getEntry($this->product);
            if ($entry === null) {
                continue;
            }

            $completenessValue += $entry->getCompleteness()->getValue();
        }

        return new Percent($completenessValue / $tasksCount);
    }
}
