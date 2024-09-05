<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Strategy\Storage;

use Ibexa\Contracts\Personalization\Criteria\CriteriaInterface;
use Ibexa\Contracts\Personalization\Storage\DataSourceInterface;
use Ibexa\Contracts\Personalization\Value\ItemGroupListInterface;
use Ibexa\Personalization\Exception\UnsupportedGroupItemStrategy;
use Traversable;

final class GroupItemStrategyDispatcher implements GroupItemStrategyDispatcherInterface
{
    /** @var iterable<\Ibexa\Personalization\Strategy\Storage\GroupItemStrategyInterface> */
    private iterable $groupItemStrategies;

    /**
     * @param iterable<\Ibexa\Personalization\Strategy\Storage\GroupItemStrategyInterface> $groupItemStrategies
     */
    public function __construct(iterable $groupItemStrategies)
    {
        $this->groupItemStrategies = $groupItemStrategies;
    }

    public function getGroupList(
        DataSourceInterface $source,
        CriteriaInterface $criteria,
        string $groupBy
    ): ItemGroupListInterface {
        $strategies = $this->groupItemStrategies instanceof Traversable
            ? iterator_to_array($this->groupItemStrategies)
            : $this->groupItemStrategies;

        if (!isset($strategies[$groupBy])) {
            throw new UnsupportedGroupItemStrategy(
                $groupBy,
                array_keys($strategies)
            );
        }

        return $strategies[$groupBy]->getGroupList($source, $criteria);
    }
}

class_alias(GroupItemStrategyDispatcher::class, 'EzSystems\EzRecommendationClient\Strategy\Storage\GroupItemStrategyDispatcher');
