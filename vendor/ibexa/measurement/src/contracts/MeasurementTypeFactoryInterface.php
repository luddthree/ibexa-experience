<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Measurement;

use Ibexa\Contracts\Measurement\Value\Definition\MeasurementInterface;

interface MeasurementTypeFactoryInterface
{
    /**
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    public function buildType(string $typeName): MeasurementInterface;

    public function hasType(string $typeName): bool;

    /**
     * @return string[]
     */
    public function getAvailableTypes(): array;
}
