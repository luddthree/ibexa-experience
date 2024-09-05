<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\UnitConverter\Exception;

use Ibexa\Contracts\Measurement\Value\Definition\UnitInterface;
use Ibexa\Contracts\Measurement\Value\ValueInterface;
use LogicException;
use Throwable;

final class MissingConverterException extends LogicException implements UnitConversionException
{
    public function __construct(ValueInterface $value, UnitInterface $destinationUnit, Throwable $previous = null)
    {
        $message = sprintf(
            'Missing converter from %s to %s for object of class %s',
            $value->getUnit()->getIdentifier(),
            $destinationUnit->getIdentifier(),
            get_class($value),
        );

        parent::__construct($message, 0, $previous);
    }
}
