<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\SiteAccess;

use Ibexa\AdminUi\Siteaccess\AbstractSiteaccessPreviewVoter;
use Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Throwable;

final class DashboardSiteAccessPreviewVoter extends AbstractSiteaccessPreviewVoter implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private LocationService $locationService;

    public function __construct(
        ConfigResolverInterface $configResolver,
        RepositoryConfigurationProvider $repositoryConfigurationProvider,
        LocationService $locationService,
        ?LoggerInterface $logger = null
    ) {
        parent::__construct($configResolver, $repositoryConfigurationProvider);

        $this->locationService = $locationService;
        $this->logger = $logger ?? new NullLogger();
    }

    protected function getRootLocationIds(string $siteAccess): array
    {
        $rootLocationIds = [];
        $dashboardContainerRemoteId = $this->configResolver->getParameter('dashboard.container_remote_id');

        if (null === $dashboardContainerRemoteId) {
            return $rootLocationIds;
        }

        try {
            $rootLocationIds[] = $this->locationService->loadLocationByRemoteId($dashboardContainerRemoteId)->getId();
        } catch (NotFoundException | UnauthorizedException $exception) {
            $this->logger->debug(
                'Root location for dashboards could not be loaded: ' . $exception->getMessage(),
                [
                    'exception' => $exception,
                ]
            );
        } catch (Throwable $exception) {
            $this->logger->error(
                'Root location for dashboards could not be loaded',
                [
                    'exception' => $exception,
                ]
            );
        }

        return $rootLocationIds;
    }
}
