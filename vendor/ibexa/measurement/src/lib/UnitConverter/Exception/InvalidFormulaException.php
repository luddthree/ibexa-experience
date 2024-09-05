<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\UnitConverter\Exception;

use RuntimeException;
use Throwable;

final class InvalidFormulaException extends RuntimeException implements UnitConversionException
{
    public function __construct(string $formula, string $message, Throwable $previous = null)
    {
        parent::__construct(sprintf('Invalid formula: "%s". %s.', $formula, $message), 0, $previous);
    }
}
