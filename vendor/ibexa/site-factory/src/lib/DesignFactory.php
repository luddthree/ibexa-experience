<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory;

use Ibexa\Contracts\SiteFactory\Values\Design\Template;
use Ibexa\SiteFactory\Provider\SiteFactoryConfigurationProvider;

class DesignFactory
{
    /** @var \Ibexa\SiteFactory\Provider\SiteFactoryConfigurationProvider */
    private $configurationProvider;

    public function __construct(
        SiteFactoryConfigurationProvider $configurationProvider
    ) {
        $this->configurationProvider = $configurationProvider;
    }

    public function getDesignRegistry(): DesignRegistry
    {
        $templates = [];
        foreach ($this->configurationProvider->getTemplatesConfiguration() as $templateConfiguration) {
            $templates[] = Template::fromConfiguration($templateConfiguration);
        }

        return new DesignRegistry($templates);
    }
}

class_alias(DesignFactory::class, 'EzSystems\EzPlatformSiteFactory\DesignFactory');
