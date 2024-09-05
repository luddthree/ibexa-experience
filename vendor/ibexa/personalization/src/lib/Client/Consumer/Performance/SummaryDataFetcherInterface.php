<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Performance;

use Psr\Http\Message\ResponseInterface;

interface SummaryDataFetcherInterface
{
    public function fetchRecommendationSummary(
        int $customerId,
        string $licenseKey,
        ?string $duration = null
    ): ResponseInterface;
}

class_alias(SummaryDataFetcherInterface::class, 'Ibexa\Platform\Personalization\Client\Consumer\Performance\SummaryDataFetcherInterface');
