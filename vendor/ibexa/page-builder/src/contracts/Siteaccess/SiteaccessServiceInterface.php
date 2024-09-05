<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\PageBuilder\Siteaccess;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;

interface SiteaccessServiceInterface
{
    public function getRootLocation(string $siteaccess): ?Location;

    /**
     * @param string $siteaccess
     *
     * @return array
     */
    public function getLanguages(string $siteaccess): array;

    /**
     * @param array $siteaccesses
     *
     * @return array
     */
    public function filterAvailableSiteaccesses(array $siteaccesses): array;
}

class_alias(SiteaccessServiceInterface::class, 'EzSystems\EzPlatformPageBuilder\Siteaccess\SiteaccessServiceInterface');
