<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service\Setting;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\SettingService as APISettingService;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Personalization\Client\Consumer\Support\AcceptanceCheckDataFetcherInterface;
use Ibexa\Personalization\Client\Consumer\Support\StoreCustomerDataSenderInterface;
use Ibexa\Personalization\Client\Consumer\Support\TermsAndConditionsDataFetcherInterface;
use Ibexa\Personalization\SiteAccess\ScopeParameterResolver;
use Ibexa\Personalization\Value\Support\AcceptanceStatus;
use Ibexa\Personalization\Value\Support\TermsAndConditions;
use Symfony\Component\HttpFoundation\RequestStack;

final class SettingService implements SettingServiceInterface
{
    private const SETTING_GROUP = 'personalization';
    private const INSTALLATION_KEY_IDENTIFIER = 'installation_key';
    private const CUSTOMER_ID_IDENTIFIER = 'customer_id';
    private const LICENSE_KEY_IDENTIFIER = 'license_key';

    private APISettingService $settingService;

    private SiteAccessServiceInterface $siteAccessService;

    private AcceptanceCheckDataFetcherInterface $acceptanceCheckDataFetcher;

    private StoreCustomerDataSenderInterface $storeCustomerDataSender;

    private TermsAndConditionsDataFetcherInterface $termsAndConditionsDataFetcher;

    private ScopeParameterResolver $scopeParameterResolver;

    private RequestStack $requestStack;

    public function __construct(
        APISettingService $settingService,
        SiteAccessServiceInterface $siteAccessService,
        ScopeParameterResolver $scopeParameterResolver,
        RequestStack $requestStack,
        AcceptanceCheckDataFetcherInterface $acceptanceCheckDataFetcher,
        StoreCustomerDataSenderInterface $storeCustomerDataSender,
        TermsAndConditionsDataFetcherInterface $termsAndConditionsDataFetcher
    ) {
        $this->settingService = $settingService;
        $this->siteAccessService = $siteAccessService;
        $this->scopeParameterResolver = $scopeParameterResolver;
        $this->requestStack = $requestStack;
        $this->acceptanceCheckDataFetcher = $acceptanceCheckDataFetcher;
        $this->storeCustomerDataSender = $storeCustomerDataSender;
        $this->termsAndConditionsDataFetcher = $termsAndConditionsDataFetcher;
    }

    public function getInstallationKey(): ?string
    {
        $setting = $this->settingService->loadSetting(
            self::SETTING_GROUP,
            self::INSTALLATION_KEY_IDENTIFIER
        );

        if (!empty($setting->value)) {
            return (string)$setting->value;
        }

        return null;
    }

    public function setInstallationKey(string $installationKey): void
    {
        $setting = $this->settingService->loadSetting(
            self::SETTING_GROUP,
            self::INSTALLATION_KEY_IDENTIFIER
        );

        $settingUpdateStruct = $this->settingService->newSettingUpdateStruct();

        $settingUpdateStruct->setValue($installationKey);

        $this->settingService->updateSetting($setting, $settingUpdateStruct);
    }

    public function isInstallationKeyFound(): bool
    {
        try {
            $installationKey = $this->getInstallationKey();
        } catch (NotFoundException $exception) {
            return false;
        }

        if (empty($installationKey)) {
            return false;
        }

        return $this->getAcceptanceStatus($installationKey)->isAccepted();
    }

    public function getCustomerId(): ?int
    {
        $setting = $this->settingService->loadSetting(
            self::SETTING_GROUP,
            self::CUSTOMER_ID_IDENTIFIER
        );

        if (empty($setting->value)) {
            return null;
        }

        return (int)$setting->value;
    }

    public function setCustomerId(int $customerId): void
    {
        $settingCreateStruct = $this->settingService->newSettingCreateStruct();
        $settingCreateStruct->setGroup(self::SETTING_GROUP);
        $settingCreateStruct->setIdentifier(self::CUSTOMER_ID_IDENTIFIER);
        $settingCreateStruct->setValue($customerId);

        $this->settingService->createSetting($settingCreateStruct);
    }

    public function getLicenseKey(): ?string
    {
        $setting = $this->settingService->loadSetting(
            self::SETTING_GROUP,
            self::LICENSE_KEY_IDENTIFIER
        );

        if (empty($setting->value)) {
            return null;
        }

        return (string)$setting->value;
    }

    public function setLicenseKey(string $licenseKey): void
    {
        $settingCreateStruct = $this->settingService->newSettingCreateStruct();
        $settingCreateStruct->setGroup(self::SETTING_GROUP);
        $settingCreateStruct->setIdentifier(self::LICENSE_KEY_IDENTIFIER);
        $settingCreateStruct->setValue($licenseKey);

        $this->settingService->createSetting($settingCreateStruct);
    }

    public function getCustomerIdFromRequest(): ?int
    {
        $currentRequest = $this->requestStack->getCurrentRequest();

        if (null !== $currentRequest && $currentRequest->attributes->has('customerId')) {
            return (int)$currentRequest->get('customerId');
        }

        return null;
    }

    public function getLicenceKeyByCustomerId(int $customerId): ?string
    {
        foreach ($this->siteAccessService->getAll() as $siteAccess) {
            if ($this->scopeParameterResolver->getCustomerIdForScope($siteAccess) === $customerId) {
                return $this->scopeParameterResolver->getLicenseKeyForScope($siteAccess);
            }
        }

        return null;
    }

    /**
     * @throws \JsonException
     */
    public function getAcceptanceStatus(string $installationKey): AcceptanceStatus
    {
        $response = $this->acceptanceCheckDataFetcher->fetchAcceptanceCheck($installationKey);

        return AcceptanceStatus::fromArray(
            json_decode(
                $response->getBody()->getContents(),
                true,
                512,
                JSON_THROW_ON_ERROR
            )
        );
    }

    /**
     * @throws \JsonException
     */
    public function acceptTermsAndConditions(string $installationKey, string $username, string $email): void
    {
        $acceptanceStatus = $this->getAcceptanceStatus($installationKey);

        if (!$acceptanceStatus->isAccepted()) {
            $this->storeCustomerDataSender->sendStoreCustomerData(
                $installationKey,
                $username,
                $email
            );
        }
    }

    /**
     * @throws \JsonException
     */
    public function getTermsAndConditions(): TermsAndConditions
    {
        $response = $this->termsAndConditionsDataFetcher->fetchTermsAndConditions();

        return TermsAndConditions::fromArray(
            json_decode(
                $response->getBody()->getContents(),
                true,
                512,
                JSON_THROW_ON_ERROR
            )
        );
    }

    public function hasCredentials(): bool
    {
        $customerId = $this->getCustomerIdFromRequest();

        return null !== $customerId
            && !empty($this->getLicenceKeyByCustomerId($customerId));
    }

    public function isAccountCreated(): bool
    {
        try {
            return null !== $this->getCustomerId()
                && null !== $this->getLicenseKey();
        } catch (NotFoundException $exception) {
            return false;
        }
    }
}

class_alias(SettingService::class, 'Ibexa\Platform\Personalization\Service\Setting\SettingService');
