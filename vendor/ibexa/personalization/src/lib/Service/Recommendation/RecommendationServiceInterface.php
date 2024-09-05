<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service\Recommendation;

use Ibexa\Personalization\Value\Recommendation\Preview;
use Ibexa\Personalization\Value\Recommendation\Request;

interface RecommendationServiceInterface
{
    public function getRecommendationsPreview(
        int $customerId,
        string $scenario,
        Request $recommendationRequest
    ): Preview;
}

class_alias(RecommendationServiceInterface::class, 'Ibexa\Platform\Personalization\Service\Recommendation\RecommendationServiceInterface');
