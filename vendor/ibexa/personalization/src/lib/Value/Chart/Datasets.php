<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Chart;

use JsonSerializable;

final class Datasets implements JsonSerializable
{
    private LegendItem $legendItem;

    /** @var array<float> */
    private array $data;

    private string $backgroundColor;

    private string $borderColor;

    private bool $fill;

    /**
     * @param array<float> $data
     */
    public function __construct(
        LegendItem $legendItem,
        array $data,
        string $backgroundColor,
        string $borderColor,
        bool $fill = false
    ) {
        $this->legendItem = $legendItem;
        $this->data = $data;
        $this->backgroundColor = $backgroundColor;
        $this->borderColor = $borderColor;
        $this->fill = $fill;
    }

    public function getLegendItem(): LegendItem
    {
        return $this->legendItem;
    }

    /**
     * @return array<float>
     */
    public function getData(): array
    {
        return $this->data;
    }

    public function getBackgroundColor(): string
    {
        return $this->backgroundColor;
    }

    public function getBorderColor(): string
    {
        return $this->borderColor;
    }

    public function isFill(): bool
    {
        return $this->fill;
    }

    /**
     * @phpstan-return array{
     *  'legendItem': LegendItem,
     *  'data': array<int|float>,
     *  'backgroundColor': string,
     *  'borderColor': string,
     *  'fill': bool,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'legendItem' => $this->getLegendItem(),
            'data' => $this->getData(),
            'backgroundColor' => $this->getBackgroundColor(),
            'borderColor' => $this->getBorderColor(),
            'fill' => $this->isFill(),
        ];
    }
}

class_alias(Datasets::class, 'Ibexa\Platform\Personalization\Value\Chart\Datasets');
