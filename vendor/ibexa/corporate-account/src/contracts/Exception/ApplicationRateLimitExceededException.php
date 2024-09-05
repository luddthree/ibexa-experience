<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Exception;

use RuntimeException;
use Symfony\Component\RateLimiter\LimiterInterface;
use Throwable;

/**
 * @internal
 */
final class ApplicationRateLimitExceededException extends RuntimeException
{
    private LimiterInterface $limiter;

    public function __construct(
        LimiterInterface $limiter,
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->limiter = $limiter;
    }

    public function getLimiter(): LimiterInterface
    {
        return $this->limiter;
    }
}
