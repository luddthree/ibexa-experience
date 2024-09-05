<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Measurement\Parser;

use Ibexa\Contracts\Measurement\Value\Definition\MeasurementInterface;
use Ibexa\Contracts\Measurement\Value\ValueInterface;

/**
 * @internal
 */
interface MeasurementParserInterface
{
    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function parse(MeasurementInterface $measurement, string $input): ValueInterface;
}
