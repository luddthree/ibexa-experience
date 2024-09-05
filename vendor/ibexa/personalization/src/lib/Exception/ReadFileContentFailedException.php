<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Exception;

use RuntimeException;
use Throwable;

final class ReadFileContentFailedException extends RuntimeException
{
    public function __construct(string $file, int $code = 0, Throwable $previous = null)
    {
        $message = 'Failed to read content of file: ' . $file;

        parent::__construct($message, $code, $previous);
    }
}
