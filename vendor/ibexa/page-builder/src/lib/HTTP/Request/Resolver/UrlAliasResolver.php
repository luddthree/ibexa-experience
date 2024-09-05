<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\HTTP\Request\Resolver;

use Ibexa\Contracts\Core\Repository\URLAliasService;
use Ibexa\Contracts\Core\Repository\Values\Content\URLAlias;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\Routing\Generator\UrlAliasGenerator;
use Ibexa\Core\MVC\Symfony\Routing\SimplifiedRequest;
use Ibexa\Core\MVC\Symfony\SiteAccess\URILexer;

class UrlAliasResolver
{
    /** @var \Ibexa\PageBuilder\HTTP\Request\Resolver\SiteaccessResolver */
    private $siteaccessResolver;

    /** @var \Ibexa\Contracts\Core\Repository\URLAliasService */
    private $urlAliasService;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    /** @var \Ibexa\Core\MVC\Symfony\Routing\Generator\UrlAliasGenerator */
    private $urlAliasGenerator;

    /**
     * @param \Ibexa\PageBuilder\HTTP\Request\Resolver\SiteaccessResolver $siteaccessResolver
     * @param \Ibexa\Contracts\Core\Repository\URLAliasService $urlAliasService
     * @param \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface $configResolver
     * @param \Ibexa\Core\MVC\Symfony\Routing\Generator\UrlAliasGenerator $urlAliasGenerator
     */
    public function __construct(
        SiteaccessResolver $siteaccessResolver,
        URLAliasService $urlAliasService,
        ConfigResolverInterface $configResolver,
        UrlAliasGenerator $urlAliasGenerator
    ) {
        $this->siteaccessResolver = $siteaccessResolver;
        $this->urlAliasService = $urlAliasService;
        $this->configResolver = $configResolver;
        $this->urlAliasGenerator = $urlAliasGenerator;
    }

    /**
     * @param \Ibexa\Core\MVC\Symfony\Routing\SimplifiedRequest $request
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\URLAlias
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Core\MVC\Exception\InvalidSiteAccessException
     */
    public function resolve(SimplifiedRequest $request): URLAlias
    {
        $siteaccess = $this->siteaccessResolver->resolve($request);

        $pathinfo = $request->pathinfo;
        if ($siteaccess->matcher instanceof URILexer) {
            // matchers are stateful, can't use original object
            $matcher = clone $siteaccess->matcher;

            if (null !== $matcher) {
                $matcher->setRequest($request);
                $pathinfo = $matcher->analyseURI($request->pathinfo);
            }
        }

        /*
         * UrlAliasService is not content.tree_root.location_id aware, therefore
         * it's necessary to prepend the path from request with proper tree root location path
        */
        $parameterName = 'content.tree_root.location_id';
        $defaultTreeRootLocationId = $this->configResolver->getParameter($parameterName);
        $siteaccessTreeRootLocationId =
            $this->configResolver->hasParameter($parameterName, null, $siteaccess->name) ?
                $this->configResolver->getParameter($parameterName, null, $siteaccess->name) :
                $defaultTreeRootLocationId;

        if ($siteaccessTreeRootLocationId !== $defaultTreeRootLocationId) {
            $contentTreeRootLocationPath = $this->urlAliasGenerator->getPathPrefixByRootLocationId($siteaccessTreeRootLocationId);
            $pathinfo = $contentTreeRootLocationPath . $pathinfo;
        }

        return $this->urlAliasService->lookup($pathinfo);
    }
}

class_alias(UrlAliasResolver::class, 'EzSystems\EzPlatformPageBuilder\HTTP\Request\Resolver\UrlAliasResolver');
