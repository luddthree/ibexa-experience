<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI\Completeness;

use ArrayIterator;
use Ibexa\Contracts\ProductCatalog\Values\Common\Percent;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Iterator;

final class Completeness implements CompletenessInterface
{
    private ProductInterface $product;

    /** @var \Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\TaskInterface[] */
    private array $tasks;

    /**
     * @param \Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\TaskInterface[] $tasks
     */
    public function __construct(ProductInterface $product, array $tasks)
    {
        $this->product = $product;
        $this->tasks = $tasks;
    }

    public function getValue(): Percent
    {
        $values = [];
        $totalWeight = 0;
        foreach ($this->tasks as $task) {
            $entry = $task->getEntry($this->product);

            if ($entry !== null) {
                $weight = $task->getWeight();

                $values[] = $entry->getCompleteness()->getValue() * $weight;
                $totalWeight += $weight;
            }
        }

        if (empty($values)) {
            return Percent::zero();
        }

        return new Percent(array_sum($values) / $totalWeight);
    }

    public function isComplete(): bool
    {
        return $this->getValue()->equals(Percent::hundred());
    }

    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->tasks);
    }
}
