<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Siteaccess;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;

interface UrlGeneratorInterface
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     * @param string|null $siteaccess
     *
     * @return string
     */
    public function getAliasUrl(Location $location, string $siteaccess = null): string;
}

class_alias(UrlGeneratorInterface::class, 'EzSystems\EzPlatformPageBuilder\Siteaccess\UrlGeneratorInterface');
