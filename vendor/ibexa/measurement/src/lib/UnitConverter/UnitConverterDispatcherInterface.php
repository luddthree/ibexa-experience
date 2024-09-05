<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\UnitConverter;

use Ibexa\Contracts\Measurement\Value\Definition\UnitInterface;
use Ibexa\Contracts\Measurement\Value\ValueInterface;

interface UnitConverterDispatcherInterface
{
    /**
     * @template T of \Ibexa\Contracts\Measurement\Value\ValueInterface
     *
     * @param T $sourceValue
     *
     * @return T
     *
     * @throws \Ibexa\Measurement\UnitConverter\Exception\UnitConversionException
     */
    public function convert(ValueInterface $sourceValue, UnitInterface $destinationUnit): ValueInterface;
}
