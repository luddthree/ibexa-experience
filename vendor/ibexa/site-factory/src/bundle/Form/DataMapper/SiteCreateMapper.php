<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory\Form\DataMapper;

use Ibexa\AdminUi\Exception\InvalidArgumentException;
use Ibexa\Bundle\SiteFactory\Form\Data\PublicAccessData;
use Ibexa\Bundle\SiteFactory\Form\Data\SiteCreateData;
use Ibexa\Bundle\SiteFactory\Form\Data\SiteMatcherConfigurationData;
use Ibexa\Contracts\AdminUi\Form\DataMapper\DataMapperInterface;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteAccessMatcherConfiguration;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteConfiguration;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteCreateStruct;

/**
 * Maps between SiteCreateStruct and SiteCreateData objects.
 */
class SiteCreateMapper implements DataMapperInterface
{
    /**
     * Maps given SiteCreateStruct object to a SiteCreateData object.
     *
     * @param \Ibexa\Contracts\SiteFactory\Values\Site\SiteCreateStruct|\Ibexa\Contracts\Core\Repository\Values\ValueObject $value
     *
     * @throws \Ibexa\AdminUi\Exception\InvalidArgumentException
     */
    public function map(ValueObject $value): SiteCreateData
    {
        if (!$value instanceof SiteCreateStruct) {
            throw new InvalidArgumentException('value', 'must be an instance of ' . SiteCreateStruct::class);
        }

        $publicAccessesUpdateData = [];
        /** @var \Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess $publicAccess */
        $treeRootLocationId = null;
        foreach ($value->publicAccesses as $publicAccess) {
            $publicAccessUpdateData = new PublicAccessData();
            $publicAccessUpdateData->setStatus($publicAccess->getStatus());

            $siteMatcherConfigurationData = new SiteMatcherConfigurationData();
            $siteMatcherConfigurationData->setHost($publicAccess->getMatcherConfiguration()->host);
            $siteMatcherConfigurationData->setPath($publicAccess->getMatcherConfiguration()->path);
            $publicAccessUpdateData->setMatcherConfiguration($siteMatcherConfigurationData);

            $publicAccessUpdateData->setConfig(['languages' => $publicAccess->getSiteConfiguration()->getLanguages()]);

            $publicAccessesUpdateData[] = $publicAccessUpdateData;
            $treeRootLocationId = $publicAccess->getSiteConfiguration()->getTreeRootLocationId();
        }

        $data = new SiteCreateData($publicAccessesUpdateData);
        $data->setSiteName($value->siteName);
        $data->setTreeRootLocationId($treeRootLocationId);

        return $data;
    }

    /**
     * Maps given SiteCreateData object to a SiteCreateStruct object.
     *
     * @param \Ibexa\Bundle\SiteFactory\Form\Data\SiteCreateData $data
     *
     * @return \Ibexa\Contracts\SiteFactory\Values\Site\SiteCreateStruct
     *
     * @throws \Ibexa\AdminUi\Exception\InvalidArgumentException
     */
    public function reverseMap($data): SiteCreateStruct
    {
        if (!$data instanceof SiteCreateData) {
            throw new InvalidArgumentException('data', 'must be an instance of ' . SiteCreateData::class);
        }

        $publicAccesses = [];
        foreach ($data->getPublicAccesses() as $publicAccess) {
            $matcherConfigurationData = $publicAccess->getMatcherConfiguration();
            $matcherConfiguration = new SiteAccessMatcherConfiguration(
                $matcherConfigurationData->getHost(),
                $matcherConfigurationData->getPath(),
            );
            $config = [];
            $config['ibexa.site_access.config.languages'] = $publicAccess->getConfig()['languages'];
            $config['ibexa.site_access.config.design'] = $data->getDesign()->design;

            $siteConfiguration = new SiteConfiguration(
                $config
            );
            $publicAccesses[] = new PublicAccess(
                null,
                null,
                $data->getDesign()->siteAccessGroup,
                $matcherConfiguration,
                $siteConfiguration,
                $publicAccess->getStatus()
            );
        }

        $siteSkeletonId = $data->getDesign()->siteSkeletonLocation->id ?? null;
        $userGroupSkeletonIds = array_column($data->getDesign()->userGroupSkeletonLocations, 'id');

        return new SiteCreateStruct(
            $data->getSiteName(),
            $data->getCopySiteSkeleton(),
            $publicAccesses,
            $data->getParentLocationId(),
            $userGroupSkeletonIds,
            null,
            $siteSkeletonId
        );
    }
}

class_alias(SiteCreateMapper::class, 'EzSystems\EzPlatformSiteFactoryBundle\Form\DataMapper\SiteCreateMapper');
