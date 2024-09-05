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
use Ibexa\Personalization\Service\Customer\CustomerInformationServiceInterface;
use Ibexa\Personalization\Value\Content\ItemTypeList;
use Ibexa\Personalization\Value\Customer\BaseInformation;
use Ibexa\Personalization\Value\Customer\Features;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;

final class CachedCustomerInformationService extends AbstractCacheServiceDecorator implements CustomerInformationServiceInterface
{
    private const EXPIRATION_TIME = 86400;
    private const BASE_CUSTOMER_INFORMATION_CACHE_KEY = 'base-customer-information';
    private const FEATURES_CACHE_KEY = 'features';

    /** @var \Ibexa\Personalization\Service\Customer\CustomerInformationServiceInterface */
    private $innerCustomerInformationService;

    public function __construct(
        TagAwareAdapterInterface $cache,
        PersistenceHandler $persistenceHandler,
        PersistenceLogger $persistenceLogger,
        CacheIdentifierGeneratorInterface $cacheIdentifierGenerator,
        CacheIdentifierSanitizer $cacheIdentifierSanitizer,
        LocationPathConverter $locationPathConverter,
        CustomerInformationServiceInterface $innerCustomerInformationService
    ) {
        parent::__construct(
            $cache,
            $persistenceHandler,
            $persistenceLogger,
            $cacheIdentifierGenerator,
            $cacheIdentifierSanitizer,
            $locationPathConverter
        );

        $this->innerCustomerInformationService = $innerCustomerInformationService;
    }

    public function getBaseInformation(int $customerId): BaseInformation
    {
        $arguments = [
            self::BASE_CUSTOMER_INFORMATION_CACHE_KEY,
            $customerId,
        ];

        $item = $this->processCacheItem(
            $this->buildCacheKey($arguments),
            $this->buildCacheTagKeys(self::BASE_CUSTOMER_INFORMATION_CACHE_KEY, $arguments),
            function () use ($customerId): BaseInformation {
                return $this->innerCustomerInformationService->getBaseInformation(
                    $customerId
                );
            },
            self::EXPIRATION_TIME
        );

        return $item->get();
    }

    public function getItemTypes(int $customerId): ItemTypeList
    {
        return $this->innerCustomerInformationService->getItemTypes($customerId);
    }

    public function getFeatures(int $customerId): Features
    {
        $arguments = [
            self::FEATURES_CACHE_KEY,
            $customerId,
        ];

        $item = $this->processCacheItem(
            $this->buildCacheKey($arguments),
            $this->buildCacheTagKeys(self::FEATURES_CACHE_KEY, $arguments),
            function () use ($customerId): Features {
                return $this->innerCustomerInformationService->getFeatures(
                    $customerId
                );
            },
            self::EXPIRATION_TIME
        );

        return $item->get();
    }
}

class_alias(CachedCustomerInformationService::class, 'Ibexa\Platform\Personalization\Cache\CachedCustomerInformationService');
