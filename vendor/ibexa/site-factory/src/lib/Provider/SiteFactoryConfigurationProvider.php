<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory\Provider;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\SiteFactory\Values\Design\TemplateConfiguration;
use Traversable;

class SiteFactoryConfigurationProvider implements SiteFactoryProviderInterface
{
    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    /** @var array */
    private $templates;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    public function __construct(
        array $templates,
        ConfigResolverInterface $configResolver,
        LocationService $locationService
    ) {
        $this->templates = $templates;
        $this->configResolver = $configResolver;
        $this->locationService = $locationService;
    }

    /**
     * @return \Ibexa\Contracts\SiteFactory\Values\Design\TemplateConfiguration[]
     */
    public function getTemplatesConfiguration(): Traversable
    {
        foreach ($this->templates as $identifier => $template) {
            $design = $this->configResolver->getParameter(
                'design',
                null,
                $template['siteaccess_group']
            );

            if ($design === null) {
                continue;
            }

            $userGroupSkeletonLocations = $this->getLocations(
                $template['user_group_skeleton_ids'],
                $template['user_group_skeleton_remote_ids']
            );

            $siteSkeletonLocation = $this->getLocation(
                $template['site_skeleton_id'],
                $template['site_skeleton_remote_id']
            );

            $parentLocation = $this->getLocation(
                $template['parent_location_id'],
                $template['parent_location_remote_id']
            );

            yield TemplateConfiguration::fromTemplate(
                $identifier,
                $design,
                $template,
                $userGroupSkeletonLocations,
                $siteSkeletonLocation,
                $parentLocation
            );
        }

        yield from [];
    }

    private function getLocation(?int $locationId, ?string $locationRemoteId): ?Location
    {
        $location = null;

        if ($locationId === null && $locationRemoteId === null) {
            return null;
        }

        if ($locationId !== null && $locationRemoteId !== null) {
            @trigger_error(
                'You should not provide both ID and remote ID for Location. Please, use one of them.',
                E_USER_WARNING
            );
        }

        if (is_string($locationRemoteId)) {
            try {
                $location = $this->locationService->loadLocationByRemoteId($locationRemoteId);
            } catch (NotFoundException | UnauthorizedException $e) {
                @trigger_error(
                    sprintf(
                        'Location with remote ID "%s" can\'t be fetched from the database. ' .
                        'Please check your templates configuration.',
                        $locationRemoteId
                    ),
                    E_USER_WARNING
                );
            }
        } elseif (is_int($locationId)) {
            try {
                $location = $this->locationService->loadLocation($locationId);
            } catch (NotFoundException | UnauthorizedException $e) {
                @trigger_error(
                    sprintf(
                        'Location with ID "%d" can\'t be fetched from the database. ' .
                        'Please check your templates configuration.',
                        $locationId
                    ),
                    E_USER_WARNING
                );
            }
        }

        return $location;
    }

    /**
     * @param int[] $locationIds
     * @param string[] $locationRemoteIds
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location[]
     */
    private function getLocations(array $locationIds, array $locationRemoteIds): array
    {
        $locations = [];

        if (empty($locationIds) && empty($locationRemoteIds)) {
            return [];
        }

        if (!empty($locationIds) && !empty($locationRemoteIds)) {
            @trigger_error(
                'You should not provide both ID and remote ID for Location. Please, use one of them.',
                E_USER_WARNING
            );
        }

        if (!empty($locationRemoteIds)) {
            foreach ($locationRemoteIds as $locationRemoteId) {
                $locations[] = $this->getLocation(null, $locationRemoteId);
            }
        } elseif (!empty($locationIds)) {
            $locations = $this->locationService->loadLocationList($locationIds);
        }

        return $locations;
    }
}

class_alias(SiteFactoryConfigurationProvider::class, 'EzSystems\EzPlatformSiteFactory\Provider\SiteFactoryConfigurationProvider');
