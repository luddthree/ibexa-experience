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

interface GroupItemStrategyDispatcherInterface
{
    public function getGroupList(
        DataSourceInterface $source,
        CriteriaInterface $criteria,
        string $groupBy
    ): ItemGroupListInterface;
}

class_alias(GroupItemStrategyDispatcherInterface::class, 'EzSystems\EzRecommendationClient\Strategy\Storage\GroupItemStrategyDispatcherInterface');
