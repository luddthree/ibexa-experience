<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Exception;

use RuntimeException;
use Throwable;

final class MalformedDataException extends RuntimeException
{
    public function __construct(string $whatIsWrong, int $code = 0, Throwable $previous = null)
    {
        parent::__construct('Recommendation engine returned malformed data. ' . $whatIsWrong, $code, $previous);
    }
}
