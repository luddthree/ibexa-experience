<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service\Chart;

use Ibexa\Personalization\Value\Chart\ChartParameters;

/**
 * @internal
 */
interface ChartServiceInterface
{
    /**
     * @return array<string, \Ibexa\Personalization\Value\Chart\Chart>
     */
    public function getCharts(int $customerId, ChartParameters $parameters): array;
}

class_alias(ChartServiceInterface::class, 'Ibexa\Platform\Personalization\Service\Chart\ChartServiceInterface');
