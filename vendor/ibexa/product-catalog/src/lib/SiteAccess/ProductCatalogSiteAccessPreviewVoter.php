<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\SiteAccess;

use Ibexa\AdminUi\Siteaccess\AbstractSiteaccessPreviewVoter;
use Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Throwable;

final class ProductCatalogSiteAccessPreviewVoter extends AbstractSiteaccessPreviewVoter implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private ConfigProviderInterface $configProvider;

    private LocationService $locationService;

    public function __construct(
        ConfigResolverInterface $configResolver,
        RepositoryConfigurationProvider $repositoryConfigurationProvider,
        ConfigProviderInterface $configProvider,
        LocationService $locationService,
        ?LoggerInterface $logger = null
    ) {
        parent::__construct($configResolver, $repositoryConfigurationProvider);

        $this->configProvider = $configProvider;
        $this->locationService = $locationService;
        $this->logger = $logger ?? new NullLogger();
    }

    protected function getRootLocationIds(string $siteaccess): array
    {
        $rootLocationIds = [];
        $rootLocationRemoteId = $this->configProvider->getEngineOption('root_location_remote_id');
        if (null !== $rootLocationRemoteId) {
            try {
                $rootLocationIds[] = $this->locationService->loadLocationByRemoteId($rootLocationRemoteId)->getId();
            } catch (NotFoundException | UnauthorizedException $exception) {
                // Do nothing
            } catch (Throwable $exception) {
                $this->logger->error(
                    'Root location for product catalog could not be loaded',
                    [
                        'exception' => $exception,
                    ]
                );
            }
        }

        return $rootLocationIds;
    }
}
