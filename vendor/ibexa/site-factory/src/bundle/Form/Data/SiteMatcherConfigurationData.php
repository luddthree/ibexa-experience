<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory\Form\Data;

final class SiteMatcherConfigurationData
{
    /** @var string|null */
    private $host;

    /** @var string|null */
    private $path;

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function setHost(?string $host): void
    {
        $this->host = $host;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): void
    {
        $this->path = $path;
    }
}

class_alias(SiteMatcherConfigurationData::class, 'EzSystems\EzPlatformSiteFactoryBundle\Form\Data\SiteMatcherConfigurationData');
