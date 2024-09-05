<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Exception;

use Throwable;

final class InvalidResponseStatusCodeException extends TransferException
{
    public function __construct(int $expectedCode, int $currentCode, ?Throwable $previous = null)
    {
        $message = 'Invalid response code. Expected: %d, %d given';

        parent::__construct(
            sprintf($message, $expectedCode, $currentCode),
            0,
            $previous
        );
    }
}
