<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Config;

use Ibexa\Personalization\Value\Config\PersonalizationClientCredentials;

final class PersonalizationClientCredentialsResolver extends CredentialsResolver
{
    public function getCredentials(?string $siteAccess = null): ?PersonalizationClientCredentials
    {
        if (!$this->hasCredentials($siteAccess)) {
            return null;
        }

        return PersonalizationClientCredentials::fromArray($this->getRequiredCredentials($siteAccess));
    }

    /**
     * @phpstan-return array{
     *  'customerId': ?int,
     *  'licenseKey': ?string,
     * }
     */
    protected function getRequiredCredentials(?string $siteAccess = null): array
    {
        return [
            PersonalizationClientCredentials::CUSTOMER_ID_KEY => $this->configResolver->getParameter(
                'personalization.authentication.customer_id',
                null,
                $siteAccess
            ),
            PersonalizationClientCredentials::LICENSE_KEY_KEY => $this->configResolver->getParameter(
                'personalization.authentication.license_key',
                null,
                $siteAccess
            ),
        ];
    }
}

class_alias(PersonalizationClientCredentialsResolver::class, 'EzSystems\EzRecommendationClient\Config\EzRecommendationClientCredentialsResolver');
