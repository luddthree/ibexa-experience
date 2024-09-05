<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\HTTP\Request\Resolver;

use Ibexa\Core\MVC\Symfony\Routing\SimplifiedRequest;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\Router;

class SiteaccessResolver
{
    /** @var \Ibexa\Core\MVC\Symfony\SiteAccess\Router */
    private $siteaccessRouter;

    /**
     * @param \Ibexa\Core\MVC\Symfony\SiteAccess\Router $siteaccessRouter
     */
    public function __construct(Router $siteaccessRouter)
    {
        $this->siteaccessRouter = $siteaccessRouter;
    }

    /**
     * @param \Ibexa\Core\MVC\Symfony\Routing\SimplifiedRequest $request
     *
     * @return \Ibexa\Core\MVC\Symfony\SiteAccess
     *
     * @throws \Ibexa\Core\MVC\Exception\InvalidSiteAccessException
     */
    public function resolve(SimplifiedRequest $request): SiteAccess
    {
        $storedSiteaccess = $this->siteaccessRouter->getSiteAccess();
        $this->siteaccessRouter->setSiteAccess(null);
        $siteaccess = $this->siteaccessRouter->match($request);
        // restoring previous state
        $this->siteaccessRouter->setSiteAccess($storedSiteaccess);

        return $siteaccess;
    }
}

class_alias(SiteaccessResolver::class, 'EzSystems\EzPlatformPageBuilder\HTTP\Request\Resolver\SiteaccessResolver');
