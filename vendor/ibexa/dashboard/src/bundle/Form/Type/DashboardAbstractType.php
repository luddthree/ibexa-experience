<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Dashboard\Form\Type;

use EmptyIterator;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\Iterator\BatchIterator;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\UserPreferenceService;
use Ibexa\Contracts\Dashboard\Iterator\BatchIteratorAdapter\DashboardFetchAdapter;
use Symfony\Component\Form\AbstractType;

abstract class DashboardAbstractType extends AbstractType
{
    protected LocationService $locationService;

    protected UserPreferenceService $userPreferenceService;

    public function __construct(
        LocationService $locationService,
        UserPreferenceService $userPreferenceService
    ) {
        $this->locationService = $locationService;
        $this->userPreferenceService = $userPreferenceService;
    }

    /**
     * @return iterable<\Ibexa\Contracts\Core\Repository\Values\Content\Location>
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    protected function getUserCustomDashboards(): iterable
    {
        try {
            $userDashboardsRemoteId = $this->userPreferenceService->getUserPreference(
                'user_dashboards'
            )->value;
        } catch (NotFoundException | UnauthorizedException $exception) {
            return new EmptyIterator();
        }

        $location = $this->locationService->loadLocationByRemoteId($userDashboardsRemoteId);
        $adapter = new DashboardFetchAdapter($this->locationService, $location);
        $iterator = new BatchIterator($adapter);
        $iterator->setBatchSize(10);

        return $iterator;
    }
}
