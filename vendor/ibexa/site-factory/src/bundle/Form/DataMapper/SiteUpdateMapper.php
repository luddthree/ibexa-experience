<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory\Form\DataMapper;

use Ibexa\AdminUi\Exception\InvalidArgumentException;
use Ibexa\Bundle\SiteFactory\Form\Data\PublicAccessUpdateData;
use Ibexa\Bundle\SiteFactory\Form\Data\SiteMatcherConfigurationData;
use Ibexa\Bundle\SiteFactory\Form\Data\SiteUpdateData;
use Ibexa\Contracts\AdminUi\Form\DataMapper\DataMapperInterface;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteAccessMatcherConfiguration;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteConfiguration;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteUpdateStruct;

/**
 * Maps between SiteUpdateStruct and SiteUpdateData objects.
 */
class SiteUpdateMapper implements DataMapperInterface
{
    /**
     * Maps given SiteUpdateStruct object to a SiteUpdateData object.
     *
     * @param \Ibexa\Contracts\SiteFactory\Values\Site\SiteUpdateStruct|\Ibexa\Contracts\Core\Repository\Values\ValueObject $value
     *
     * @throws \Ibexa\AdminUi\Exception\InvalidArgumentException
     */
    public function map(ValueObject $value): SiteUpdateData
    {
        if (!$value instanceof SiteUpdateStruct) {
            throw new InvalidArgumentException('value', 'must be an instance of ' . SiteUpdateStruct::class);
        }
        $publicAccessesUpdateData = [];
        /** @var \Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess $publicAccess */
        $treeRootLocationId = null;
        foreach ($value->publicAccesses as $publicAccess) {
            $publicAccessUpdateData = new PublicAccessUpdateData();
            $publicAccessUpdateData->setStatus($publicAccess->getStatus());

            $siteMatcherConfigurationData = new SiteMatcherConfigurationData();
            $siteMatcherConfigurationData->setHost($publicAccess->getMatcherConfiguration()->host);
            $siteMatcherConfigurationData->setPath($publicAccess->getMatcherConfiguration()->path);
            $publicAccessUpdateData->setMatcherConfiguration($siteMatcherConfigurationData);

            $publicAccessUpdateData->setConfig(['languages' => $publicAccess->getSiteConfiguration()->getLanguages()]);

            $publicAccessesUpdateData[] = $publicAccessUpdateData;
            $treeRootLocationId = $publicAccess->getSiteConfiguration()->getTreeRootLocationId();
        }

        $data = new SiteUpdateData($publicAccessesUpdateData);
        $data->setSiteName($value->siteName);
        $data->setTreeRootLocationId($treeRootLocationId);

        return $data;
    }

    /**
     * Maps given SiteUpdateData object to a SiteUpdateStruct object.
     *
     * @param \Ibexa\Bundle\SiteFactory\Form\Data\SiteUpdateData $data
     *
     * @return \Ibexa\Contracts\SiteFactory\Values\Site\SiteUpdateStruct
     *
     * @throws \Ibexa\AdminUi\Exception\InvalidArgumentException
     */
    public function reverseMap($data): SiteUpdateStruct
    {
        if (!$data instanceof SiteUpdateData) {
            throw new InvalidArgumentException('data', 'must be an instance of ' . SiteUpdateData::class);
        }

        $publicAccesses = [];
        /** @var \Ibexa\Bundle\SiteFactory\Form\Data\PublicAccessUpdateData $publicAccess */
        foreach ($data->getPublicAccesses() as $publicAccess) {
            $matcherConfigurationData = $publicAccess->getMatcherConfiguration();
            $matcherConfiguration = new SiteAccessMatcherConfiguration(
                $matcherConfigurationData->getHost(),
                $matcherConfigurationData->getPath(),
            );
            $config = [];
            $config['ibexa.site_access.config.languages'] = $publicAccess->getConfig()['languages'];
            $config['ibexa.site_access.config.content.tree_root.location_id'] = $data->getTreeRootLocationId();
            $config['ibexa.site_access.config.design'] = $data->getDesign()->design;

            $siteConfiguration = new SiteConfiguration(
                $config
            );
            $publicAccesses[] = new PublicAccess(
                $publicAccess->getIdentifier(),
                null,
                $data->getDesign()->siteAccessGroup,
                $matcherConfiguration,
                $siteConfiguration,
                $publicAccess->getStatus()
            );
        }

        return new SiteUpdateStruct(
            $data->getSiteName(),
            $publicAccesses
        );
    }
}

class_alias(SiteUpdateMapper::class, 'EzSystems\EzPlatformSiteFactoryBundle\Form\DataMapper\SiteUpdateMapper');
