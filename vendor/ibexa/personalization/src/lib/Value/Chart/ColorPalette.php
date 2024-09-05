<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Chart;

final class ColorPalette
{
    public const REVENUE_CHART_COLOR = '#FCAE42';
    public const ITEMS_PURCHASED_CHART_COLOR = '#47BEDB';
    public const DEFAULT_CHART_COLOR_PALETTE = [
        '#FCAE42',
        '#47BEDB',
        '#F18A6B',
        '#004D5D',
        '#DB0032',
        '#131C26',
        '#FF4713',
        '#AE1164',
        '#FCAE4280',
        '#47BEDB80',
        '#F18A6B80',
        '#004D5D80',
        '#DB003280',
        '#131C2680',
        '#FF471380',
        '#AE116480',
    ];
}

class_alias(ColorPalette::class, 'Ibexa\Platform\Personalization\Value\Chart\ColorPalette');
