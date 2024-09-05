<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Chart;

use JsonSerializable;

final class Chart implements JsonSerializable
{
    /** @var array<string> */
    private array $labels;

    /** @var array<\Ibexa\Personalization\Value\Chart\Datasets> */
    private array $datasets;

    /** @var array<\Ibexa\Personalization\Value\Chart\Summary> */
    private array $summary;

    /**
     * @param array<string> $labels
     * @param array<\Ibexa\Personalization\Value\Chart\Datasets> $datasets
     * @param array<\Ibexa\Personalization\Value\Chart\Summary> $summary
     */
    public function __construct(
        array $labels,
        array $datasets,
        array $summary
    ) {
        $this->labels = array_unique($labels);
        $this->datasets = $datasets;
        $this->summary = $summary;
    }

    /**
     * @return array<string>
     */
    public function getLabels(): array
    {
        return $this->labels;
    }

    /**
     * @return array<\Ibexa\Personalization\Value\Chart\Datasets>
     */
    public function getDatasets(): array
    {
        return $this->datasets;
    }

    /**
     * @return array<\Ibexa\Personalization\Value\Chart\Summary>
     */
    public function getSummary(): array
    {
        return $this->summary;
    }

    /**
     * @return array{
     *  'labels': array<string>,
     *  'datasets': array<\Ibexa\Personalization\Value\Chart\Datasets>,
     *  'summary': array<\Ibexa\Personalization\Value\Chart\Summary>,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'labels' => $this->getLabels(),
            'datasets' => $this->getDatasets(),
            'summary' => $this->getSummary(),
        ];
    }
}

class_alias(Chart::class, 'Ibexa\Platform\Personalization\Value\Chart\Chart');
