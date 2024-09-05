<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Chart;

final class Struct
{
    /** @var array<string> */
    private array $labels;

    private DataList $dataList;

    /** @var \Ibexa\Personalization\Value\Chart\Summary[] */
    private array $summary;

    /** @var string[] */
    private array $colorPalette;

    private bool $allowZeroValues;

    /**
     * @param array<string> $labels
     * @param array<\Ibexa\Personalization\Value\Chart\Summary> $summary
     * @param array<string> $colorPalette
     */
    public function __construct(
        array $labels,
        DataList $dataList,
        array $summary,
        array $colorPalette = ColorPalette::DEFAULT_CHART_COLOR_PALETTE,
        bool $allowZeroValues = true
    ) {
        $this->labels = $labels;
        $this->dataList = $dataList;
        $this->summary = $summary;
        $this->colorPalette = $colorPalette;
        $this->allowZeroValues = $allowZeroValues;
    }

    /**
     * @return array<string>
     */
    public function getLabels(): array
    {
        return $this->labels;
    }

    /**
     * @return DataList<\Ibexa\Personalization\Value\Chart\Data>
     */
    public function getDataList(): DataList
    {
        return $this->dataList;
    }

    /**
     * @return array<\Ibexa\Personalization\Value\Chart\Summary>
     */
    public function getSummary(): array
    {
        return $this->summary;
    }

    /**
     * @return array<string>
     */
    public function getColorPalette(): array
    {
        return $this->colorPalette;
    }

    public function isAllowZeroValues(): bool
    {
        return $this->allowZeroValues;
    }
}

class_alias(Struct::class, 'Ibexa\Platform\Personalization\Value\Chart\Struct');
