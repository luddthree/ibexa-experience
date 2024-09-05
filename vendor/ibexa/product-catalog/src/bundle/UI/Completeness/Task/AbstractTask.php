<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI\Completeness\Task;

use Ibexa\Contracts\ProductCatalog\Values\Common\Percent;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Traversable;

abstract class AbstractTask implements TaskInterface
{
    /**
     * @param iterable<object> $iterable
     */
    protected function getIterableCount(iterable $iterable): int
    {
        if ($iterable instanceof Traversable) {
            $iterable = iterator_to_array($iterable);
        }

        return count($iterable);
    }

    protected function getTaskCompletenessValue(ProductInterface $product): Percent
    {
        $completenessValue = 0.0;
        $subtaskGroups = $this->getSubtaskGroups($product);

        if (empty($subtaskGroups)) {
            return Percent::hundred();
        }

        foreach ($subtaskGroups as $group) {
            $completenessValue += $group->getCompleteness()->getValue();
        }

        return new Percent($completenessValue / count($subtaskGroups));
    }
}
