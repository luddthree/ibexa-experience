<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Performance;

use Ibexa\Personalization\Value\DateTimeRange;
use Psr\Http\Message\ResponseInterface;

interface RevenueDetailsDataFetcherInterface
{
    public function fetchRevenueDetailsList(
        int $customerId,
        string $licenseKey,
        DateTimeRange $dateTimeRange
    ): ResponseInterface;

    public function fetchRevenueReport(
        int $customerId,
        string $licenseKey,
        DateTimeRange $dateTimeRange,
        string $format,
        ?string $email = null
    ): ResponseInterface;
}

class_alias(RevenueDetailsDataFetcherInterface::class, 'Ibexa\Platform\Personalization\Client\Consumer\Performance\RevenueDetailsDataFetcherInterface');
