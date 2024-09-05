<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory\Form\DataTransformer;

use Ibexa\Bundle\SiteFactory\Form\Data\SiteMatcherConfigurationData;
use Symfony\Component\Form\DataTransformerInterface;

final class DomainNameTransformer implements DataTransformerInterface
{
    /**
     * @param \Ibexa\Bundle\SiteFactory\Form\Data\SiteMatcherConfigurationData|null $siteMatcherConfigurationData
     */
    public function transform($siteMatcherConfigurationData): ?SiteMatcherConfigurationData
    {
        return $siteMatcherConfigurationData;
    }

    /**
     * @param \Ibexa\Bundle\SiteFactory\Form\Data\SiteMatcherConfigurationData|null $siteMatcherConfigurationData
     */
    public function reverseTransform($siteMatcherConfigurationData): ?SiteMatcherConfigurationData
    {
        if (empty($siteMatcherConfigurationData)) {
            return null;
        }

        /** regexp for detecting http/https prefix in a matcher */
        $pattern = '/^(https?:\/\/)/i';
        $host = preg_replace($pattern, '', $siteMatcherConfigurationData->getHost());
        $siteMatcherConfigurationData->setHost($host);

        return $siteMatcherConfigurationData;
    }
}

class_alias(DomainNameTransformer::class, 'EzSystems\EzPlatformSiteFactoryBundle\Form\DataTransformer\DomainNameTransformer');
