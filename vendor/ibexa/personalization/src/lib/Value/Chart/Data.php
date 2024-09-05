<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Chart;

final class Data
{
    private LegendItem $legendItem;

    /** @var array<int|float> */
    private array $collection;

    /**
     * @param array<int|float> $collection
     */
    public function __construct(
        LegendItem $legendItem,
        array $collection
    ) {
        $this->legendItem = $legendItem;
        $this->collection = $collection;
    }

    public function getLegendItem(): LegendItem
    {
        return $this->legendItem;
    }

    /**
     * @return array<int|float>
     */
    public function getCollection(): array
    {
        return $this->collection;
    }
}

class_alias(Data::class, 'Ibexa\Platform\Personalization\Value\Chart\Data');
