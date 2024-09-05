<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory\Form\Data;

use Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess;
use Ibexa\Contracts\SiteFactory\Values\Site\Site;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteAccessMatcherConfiguration;

final class SiteUpdateData extends SiteData
{
    public static function fromSite(Site $site)
    {
        $publicAccessesData = null;
        foreach ($site->publicAccesses as $publicAccess) {
            $languages = $publicAccess->getSiteConfiguration()->getLanguages();
            $publicAccessUpdateData = self::getPublicAccessUpdateData($publicAccess);
            $matcherConfiguration = self::getSiteMatcherConfigurationData($publicAccess->matcherConfiguration);
            $publicAccessUpdateData->setMatcherConfiguration($matcherConfiguration);
            $publicAccessUpdateData->setConfig(['languages' => $languages]);
            $publicAccessesData[] = $publicAccessUpdateData;
        }

        $siteUpdateData = new self($publicAccessesData);
        $siteUpdateData->setDesign($site->template);
        $siteUpdateData->setTreeRootLocationId($site->getTreeRootLocationId());
        $siteUpdateData->setSiteName($site->name);

        return $siteUpdateData;
    }

    private static function getPublicAccessUpdateData(PublicAccess $publicAccess): PublicAccessUpdateData
    {
        $publicAccessUpdateData = new PublicAccessUpdateData();
        $publicAccessUpdateData->setIdentifier($publicAccess->identifier);
        $publicAccessUpdateData->setStatus($publicAccess->status);

        return $publicAccessUpdateData;
    }

    private static function getSiteMatcherConfigurationData(SiteAccessMatcherConfiguration $matcherConfiguration): SiteMatcherConfigurationData
    {
        $matcherConfigurationData = new SiteMatcherConfigurationData();
        $matcherConfigurationData->setHost($matcherConfiguration->host);
        $matcherConfigurationData->setPath($matcherConfiguration->path);

        return $matcherConfigurationData;
    }
}

class_alias(SiteUpdateData::class, 'EzSystems\EzPlatformSiteFactoryBundle\Form\Data\SiteUpdateData');
