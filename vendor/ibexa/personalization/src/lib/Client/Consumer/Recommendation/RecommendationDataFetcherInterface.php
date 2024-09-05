<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Recommendation;

use Ibexa\Personalization\Value\Recommendation\Request;
use Psr\Http\Message\ResponseInterface;

interface RecommendationDataFetcherInterface
{
    public function fetchRecommendations(
        int $customerId,
        string $licenseKey,
        string $scenarioName,
        Request $recommendationRequest
    ): ResponseInterface;

    public function getRecommendationPreviewUri(
        int $customerId,
        string $scenarioName,
        Request $recommendationRequest
    ): string;
}

class_alias(RecommendationDataFetcherInterface::class, 'Ibexa\Platform\Personalization\Client\Consumer\Recommendation\RecommendationDataFetcherInterface');
