<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Personalization\Templating\Twig\Functions;

use Ibexa\Personalization\Config\CredentialsResolverInterface;
use Twig\Extension\RuntimeExtensionInterface;

final class Recommendation implements RuntimeExtensionInterface
{
    private CredentialsResolverInterface $credentialsResolver;

    public function __construct(CredentialsResolverInterface $credentialsResolver)
    {
        $this->credentialsResolver = $credentialsResolver;
    }

    public function isRecommendationsEnabled(): bool
    {
        return $this->credentialsResolver->hasCredentials();
    }
}

class_alias(Recommendation::class, 'EzSystems\EzRecommendationClientBundle\Templating\Twig\Functions\Recommendation');
