<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service\Setting;

use Ibexa\Personalization\Value\Support\AcceptanceStatus;
use Ibexa\Personalization\Value\Support\TermsAndConditions;

final class DefaultSiteAccessSettingService implements SettingServiceInterface
{
    /** @var \Ibexa\Personalization\Service\Setting\SettingServiceInterface */
    private $innerService;

    /** @var string */
    private $defaultSiteAccessName;

    public function __construct(
        SettingServiceInterface $innerService,
        string $defaultSiteAccessName
    ) {
        $this->innerService = $innerService;
        $this->defaultSiteAccessName = $defaultSiteAccessName;
    }

    public function getInstallationKey(): ?string
    {
        return $this->innerService->getInstallationKey();
    }

    public function setInstallationKey(string $installationKey): void
    {
        $this->innerService->setInstallationKey($installationKey);
    }

    public function isInstallationKeyFound(): bool
    {
        return $this->innerService->isInstallationKeyFound();
    }

    public function getCustomerId(): ?int
    {
        return $this->innerService->getCustomerId();
    }

    public function setCustomerId(int $customerId): void
    {
        $this->innerService->setCustomerId($customerId);
    }

    public function getLicenseKey(): ?string
    {
        return $this->innerService->getLicenseKey();
    }

    public function setLicenseKey(string $licenseKey): void
    {
        $this->innerService->setLicenseKey($licenseKey);
    }

    public function getCustomerIdFromRequest(): ?int
    {
        return $this->innerService->getCustomerIdFromRequest();
    }

    public function getLicenceKeyByCustomerId(int $customerId): ?string
    {
        return $this->innerService->getLicenceKeyByCustomerId($customerId);
    }

    public function getAcceptanceStatus(string $installationKey): AcceptanceStatus
    {
        return $this->innerService->getAcceptanceStatus($installationKey);
    }

    public function acceptTermsAndConditions(string $installationKey, string $username, string $email): void
    {
        $this->innerService->acceptTermsAndConditions($installationKey, $username, $email);
    }

    public function getTermsAndConditions(): TermsAndConditions
    {
        return $this->innerService->getTermsAndConditions();
    }

    public function hasCredentials(): bool
    {
        return $this->innerService->hasCredentials();
    }

    public function isAccountCreated(): bool
    {
        return $this->innerService->isAccountCreated();
    }
}

class_alias(DefaultSiteAccessSettingService::class, 'Ibexa\Platform\Personalization\Service\Setting\DefaultSiteAccessSettingService');
