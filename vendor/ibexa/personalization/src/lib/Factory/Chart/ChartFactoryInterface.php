<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Factory\Chart;

use Ibexa\Personalization\Value\Chart\Chart;
use Ibexa\Personalization\Value\Chart\Struct;

/**
 * @internal
 */
interface ChartFactoryInterface
{
    public function create(Struct $chartStruct): Chart;
}

class_alias(ChartFactoryInterface::class, 'Ibexa\Platform\Personalization\Factory\Chart\ChartFactoryInterface');
