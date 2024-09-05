<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Exception;

use RuntimeException;
use Throwable;

final class FailedToFetchReportException extends RuntimeException
{
    public function __construct(string $reportName, int $code = 0, Throwable $previous = null)
    {
        parent::__construct(
            sprintf('Failed to fetch %s report', $reportName),
            $code,
            $previous
        );
    }
}

class_alias(FailedToFetchReportException::class, 'Ibexa\Platform\Personalization\Exception\FailedToFetchReportException');
