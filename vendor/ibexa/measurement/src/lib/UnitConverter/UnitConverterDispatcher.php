<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\UnitConverter;

use Ibexa\Contracts\Measurement\Value\Definition\UnitInterface;
use Ibexa\Contracts\Measurement\Value\ValueInterface;
use Ibexa\Measurement\UnitConverter\Exception\MissingConverterException;

final class UnitConverterDispatcher implements UnitConverterDispatcherInterface
{
    /** @var iterable<\Ibexa\Contracts\Measurement\Converter\UnitConverterInterface> */
    private iterable $converters;

    /**
     * @param iterable<\Ibexa\Contracts\Measurement\Converter\UnitConverterInterface> $converters
     */
    public function __construct(iterable $converters)
    {
        $this->converters = $converters;
    }

    public function convert(
        ValueInterface $sourceValue,
        UnitInterface $destinationUnit
    ): ValueInterface {
        foreach ($this->converters as $converter) {
            if ($converter->supports($sourceValue, $destinationUnit)) {
                return $converter->convert($sourceValue, $destinationUnit);
            }
        }

        throw new MissingConverterException($sourceValue, $destinationUnit);
    }
}
