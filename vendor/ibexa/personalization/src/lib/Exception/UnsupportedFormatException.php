<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Exception;

use RuntimeException;
use Throwable;

final class UnsupportedFormatException extends RuntimeException
{
    public function __construct(string $format, array $allowedFormats, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf(
            'Given format: %s is unsupported. Allowed formats: %s',
            $format,
            implode(', ', $allowedFormats)
        ), $code, $previous);
    }
}

class_alias(UnsupportedFormatException::class, 'Ibexa\Platform\Personalization\Exception\UnsupportedFormatException');
