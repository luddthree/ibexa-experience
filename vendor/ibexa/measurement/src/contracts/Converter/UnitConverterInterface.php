<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Measurement\Converter;

use Ibexa\Contracts\Measurement\Value\Definition\UnitInterface;
use Ibexa\Contracts\Measurement\Value\ValueInterface;

interface UnitConverterInterface
{
    public function supports(ValueInterface $sourceValue, UnitInterface $toUnit): bool;

    /**
     * @template T of \Ibexa\Contracts\Measurement\Value\ValueInterface
     *
     * @param T $sourceValue
     *
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     *
     * @return T
     */
    public function convert(
        ValueInterface $sourceValue,
        UnitInterface $targetUnit
    ): ValueInterface;
}
