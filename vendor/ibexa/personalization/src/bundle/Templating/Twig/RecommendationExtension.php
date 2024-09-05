<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Personalization\Templating\Twig;

use Ibexa\Bundle\Personalization\Templating\Twig\Functions\Recommendation;
use Ibexa\Bundle\Personalization\Templating\Twig\Functions\UserTracking;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class RecommendationExtension extends AbstractExtension
{
    /**
     * Returns a list of functions to add to the existing list.
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ez_recommendation_enabled',
                [Recommendation::class, 'isRecommendationsEnabled'],
                [
                    'deprecated' => '4.0',
                    'alternative' => 'ibexa_recommendation_enabled',
                ]
            ),
            new TwigFunction(
                'ez_recommendation_track_user',
                [UserTracking::class, 'trackUser'],
                [
                    'is_safe' => ['html'],
                    'deprecated' => '4.0',
                    'alternative' => 'ibexa_recommendation_track_user',
                ]
            ),
            new TwigFunction(
                'ibexa_recommendation_enabled',
                [Recommendation::class, 'isRecommendationsEnabled']
            ),
            new TwigFunction(
                'ibexa_recommendation_track_user',
                [UserTracking::class, 'trackUser'],
                [
                    'is_safe' => ['html'],
                ]
            ),
        ];
    }
}

class_alias(RecommendationExtension::class, 'EzSystems\EzRecommendationClientBundle\Templating\Twig\RecommendationExtension');
