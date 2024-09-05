<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Cache;

use Ibexa\Contracts\Core\Persistence\Handler as PersistenceHandler;
use Ibexa\Core\Persistence\Cache\Identifier\CacheIdentifierGeneratorInterface;
use Ibexa\Core\Persistence\Cache\Identifier\CacheIdentifierSanitizer;
use Ibexa\Core\Persistence\Cache\LocationPathConverter;
use Ibexa\Core\Persistence\Cache\PersistenceLogger;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Ibexa\Personalization\Value\Support\AcceptanceStatus;
use Ibexa\Personalization\Value\Support\TermsAndConditions;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;

final class CachedSettingService extends AbstractCacheServiceDecorator implements SettingServiceInterface
{
    private const ACCEPTANCE_STATUS_KEY = 'recommendation-acceptance-status';
    private const TERMS_AND_CONDITIONS_KEY = 'recommendation-terms-and-conditions';
    private const CUSTOMER_ID_KEY = 'personalization-customer-id';
    private const LICENSE_KEY = 'personalization-license-key';
    private const IS_ACCOUNT_CREATED_KEY = 'personalization-created-account';
    private const EXPIRATION_TIME = 3600;

    /** @var \Ibexa\Personalization\Service\Setting\SettingServiceInterface */
    private $innerService;

    public function __construct(
        TagAwareAdapterInterface $cache,
        PersistenceHandler $persistenceHandler,
        PersistenceLogger $logger,
        CacheIdentifierGeneratorInterface $cacheIdentifierGenerator,
        CacheIdentifierSanitizer $cacheIdentifierSanitizer,
        LocationPathConverter $locationPathConverter,
        SettingServiceInterface $innerService
    ) {
        parent::__construct(
            $cache,
            $persistenceHandler,
            $logger,
            $cacheIdentifierGenerator,
            $cacheIdentifierSanitizer,
            $locationPathConverter
        );

        $this->innerService = $innerService;
    }

    public function getInstallationKey(): ?string
    {
        return $this->innerService->getInstallationKey();
    }

    public function setInstallationKey(string $installationKey): void
    {
        $this->innerService->setInstallationKey($installationKey);

        $this->removeCacheItem(
            self::ACCEPTANCE_STATUS_KEY,
            [
                self::ACCEPTANCE_STATUS_KEY,
                $installationKey,
            ]
        );
    }

    public function isInstallationKeyFound(): bool
    {
        return $this->innerService->isInstallationKeyFound();
    }

    public function getCustomerId(): ?int
    {
        $arguments = [self::CUSTOMER_ID_KEY];

        $item = $this->processCacheItem(
            $this->buildCacheKey($arguments),
            $this->buildCacheTagKeys(self::ACCEPTANCE_STATUS_KEY, $arguments),
            function (): ?int {
                return $this->innerService->getCustomerId();
            }
        );

        return $item->get();
    }

    public function setCustomerId(int $customerId): void
    {
        $this->innerService->setCustomerId($customerId);

        $this->removeCacheItem(
            self::CUSTOMER_ID_KEY,
            [self::CUSTOMER_ID_KEY]
        );

        $this->removeCacheItem(
            self::IS_ACCOUNT_CREATED_KEY,
            [self::IS_ACCOUNT_CREATED_KEY]
        );
    }

    public function getLicenseKey(): ?string
    {
        $arguments = [self::LICENSE_KEY];

        $item = $this->processCacheItem(
            $this->buildCacheKey($arguments),
            $this->buildCacheTagKeys(self::ACCEPTANCE_STATUS_KEY, $arguments),
            function (): ?string {
                return $this->innerService->getLicenseKey();
            }
        );

        return $item->get();
    }

    public function setLicenseKey(string $licenseKey): void
    {
        $this->innerService->setLicenseKey($licenseKey);

        $this->removeCacheItem(
            self::LICENSE_KEY,
            [self::LICENSE_KEY]
        );

        $this->removeCacheItem(
            self::IS_ACCOUNT_CREATED_KEY,
            [self::IS_ACCOUNT_CREATED_KEY]
        );
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
        $arguments = [
            self::ACCEPTANCE_STATUS_KEY,
            $installationKey,
        ];

        $item = $this->processCacheItem(
            $this->buildCacheKey($arguments),
            $this->buildCacheTagKeys(self::ACCEPTANCE_STATUS_KEY, $arguments),
            function () use ($installationKey): AcceptanceStatus {
                return $this->innerService->getAcceptanceStatus($installationKey);
            }
        );

        return $item->get();
    }

    public function acceptTermsAndConditions(string $installationKey, string $username, string $email): void
    {
        $this->innerService->acceptTermsAndConditions($installationKey, $username, $email);

        $this->removeCacheItem(
            self::ACCEPTANCE_STATUS_KEY,
            [
                self::ACCEPTANCE_STATUS_KEY,
                $installationKey,
            ]
        );
    }

    public function getTermsAndConditions(): TermsAndConditions
    {
        $arguments = [
            self::TERMS_AND_CONDITIONS_KEY,
        ];

        $item = $this->processCacheItem(
            $this->buildCacheKey($arguments),
            $this->buildCacheTagKeys(self::TERMS_AND_CONDITIONS_KEY, $arguments),
            function (): TermsAndConditions {
                return $this->innerService->getTermsAndConditions();
            },
            self::EXPIRATION_TIME
        );

        return $item->get();
    }

    public function hasCredentials(): bool
    {
        return $this->innerService->hasCredentials();
    }

    public function isAccountCreated(): bool
    {
        $arguments = [self::IS_ACCOUNT_CREATED_KEY];

        $item = $this->processCacheItem(
            $this->buildCacheKey($arguments),
            $this->buildCacheTagKeys(self::ACCEPTANCE_STATUS_KEY, $arguments),
            function (): bool {
                return $this->innerService->isAccountCreated();
            }
        );

        return $item->get();
    }
}

class_alias(CachedSettingService::class, 'Ibexa\Platform\Personalization\Cache\CachedSettingService');
