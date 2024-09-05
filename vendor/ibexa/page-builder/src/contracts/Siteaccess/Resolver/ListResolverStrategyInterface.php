<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\PageBuilder\Siteaccess\Resolver;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;

/**
 * @internal
 */
interface ListResolverStrategyInterface
{
    /**
     * @return \Ibexa\Core\MVC\Symfony\SiteAccess[]|null
     */
    public function getSiteAccessListForLocation(
        Location $location,
        ?int $versionNo,
        ?string $languageCode
    ): ?array;

    /**
     * @return \Ibexa\Core\MVC\Symfony\SiteAccess[]|null
     */
    public function getSiteAccessListForContent(Content $content): ?array;

    /**
     * @return \Ibexa\Core\MVC\Symfony\SiteAccess[]|null
     */
    public function getSiteAccessList(): ?array;
}
