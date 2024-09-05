<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service;

use Ibexa\Personalization\Request\BasicRecommendationRequest;
use Psr\Http\Message\ResponseInterface;

interface RecommendationServiceInterface
{
    public function getRecommendations(BasicRecommendationRequest $recommendationRequest): ?ResponseInterface;

    public function sendDeliveryFeedback(string $outputContentType): void;

    /**
     * @return \Ibexa\Personalization\Value\RecommendationItem[]
     */
    public function getRecommendationItems(array $recommendationItems): array;
}

class_alias(RecommendationServiceInterface::class, 'EzSystems\EzRecommendationClient\Service\RecommendationServiceInterface');
