<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Factory\Chart;

use ArrayIterator;
use Ibexa\Personalization\Value\Chart\Chart;
use Ibexa\Personalization\Value\Chart\Data;
use Ibexa\Personalization\Value\Chart\Datasets;
use Ibexa\Personalization\Value\Chart\Struct;
use InfiniteIterator;

final class ChartFactory implements ChartFactoryInterface
{
    public function create(Struct $chartStruct): Chart
    {
        $colorPaletteIterator = new InfiniteIterator(
            new ArrayIterator($chartStruct->getColorPalette())
        );
        $colorPaletteIterator->rewind();

        /** @var \Ibexa\Personalization\Value\Chart\Data $chartData */
        foreach ($chartStruct->getDataList() as $chartData) {
            if (
                !$chartStruct->isAllowZeroValues()
                && $this->chartHasOnlyZeroValues($chartData->getCollection())
            ) {
                continue;
            }

            $color = $colorPaletteIterator->current();

            $datasets[] = $this->createDatasets(
                $chartData,
                $color,
                $color
            );

            $colorPaletteIterator->next();
        }

        return new Chart(
            $chartStruct->getLabels(),
            $datasets ?? [],
            $chartStruct->getSummary()
        );
    }

    /**
     * @param array<int|float> $chartData
     */
    private function chartHasOnlyZeroValues(array $chartData): bool
    {
        $sum = array_sum($chartData);

        return abs($sum) < PHP_FLOAT_EPSILON;
    }

    private function createDatasets(
        Data $data,
        string $backgroundColor,
        string $borderColor
    ): Datasets {
        return new Datasets(
            $data->getLegendItem(),
            $data->getCollection(),
            $backgroundColor,
            $borderColor
        );
    }
}

class_alias(ChartFactory::class, 'Ibexa\Platform\Personalization\Factory\Chart\ChartFactory');
