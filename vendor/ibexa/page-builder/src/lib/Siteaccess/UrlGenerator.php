<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Siteaccess;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Core\MVC\Symfony\Routing\Generator\UrlAliasGenerator;

/**
 * @internal
 */
class UrlGenerator implements UrlGeneratorInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    protected $locationService;

    /** @var \Ibexa\Core\MVC\Symfony\Routing\Generator\UrlAliasGenerator */
    protected $urlAliasGenerator;

    /** @var ReverseMatcher */
    protected $reverseMatcher;

    /** @var string */
    protected $defaultSiteAccess;

    /**
     * @param \Ibexa\Contracts\Core\Repository\LocationService $locationService
     * @param \Ibexa\Core\MVC\Symfony\Routing\Generator\UrlAliasGenerator $urlAliasGenerator
     * @param ReverseMatcher $reverseMatcher
     * @param string $defaultSiteAccess
     */
    public function __construct(
        LocationService $locationService,
        UrlAliasGenerator $urlAliasGenerator,
        ReverseMatcher $reverseMatcher,
        string $defaultSiteAccess
    ) {
        $this->locationService = $locationService;
        $this->urlAliasGenerator = $urlAliasGenerator;
        $this->reverseMatcher = $reverseMatcher;
        $this->defaultSiteAccess = $defaultSiteAccess;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     * @param string|null $siteaccess
     *
     * @return string
     *
     * @internal
     */
    public function getAliasUrl(Location $location, string $siteaccess = null): string
    {
        $siteaccess = $siteaccess ?: $this->defaultSiteAccess;

        $urlRoot = $this->reverseMatcher->getUrlRoot($siteaccess);
        $urlPath = $this->urlAliasGenerator->doGenerate($location, []);

        return rtrim($urlRoot, '/') . $urlPath;
    }
}

class_alias(UrlGenerator::class, 'EzSystems\EzPlatformPageBuilder\Siteaccess\UrlGenerator');
