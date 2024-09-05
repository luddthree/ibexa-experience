<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service\Setting;

use Ibexa\Personalization\Value\Support\AcceptanceStatus;
use Ibexa\Personalization\Value\Support\TermsAndConditions;

/**
 * @internal
 */
interface SettingServiceInterface
{
    public function getInstallationKey(): ?string;

    public function setInstallationKey(string $installationKey): void;

    public function isInstallationKeyFound(): bool;

    public function getCustomerId(): ?int;

    public function setCustomerId(int $customerId): void;

    public function getLicenseKey(): ?string;

    public function setLicenseKey(string $licenseKey): void;

    public function getCustomerIdFromRequest(): ?int;

    public function getLicenceKeyByCustomerId(int $customerId): ?string;

    public function getAcceptanceStatus(string $installationKey): AcceptanceStatus;

    public function acceptTermsAndConditions(
        string $installationKey,
        string $username,
        string $email
    ): void;

    public function getTermsAndConditions(): TermsAndConditions;

    public function hasCredentials(): bool;

    public function isAccountCreated(): bool;
}

class_alias(SettingServiceInterface::class, 'Ibexa\Platform\Personalization\Service\Setting\SettingServiceInterface');
