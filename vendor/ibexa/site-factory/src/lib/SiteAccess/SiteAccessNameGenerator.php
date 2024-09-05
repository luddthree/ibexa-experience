<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory\SiteAccess;

use Ibexa\AdminUi\Siteaccess\SiteAccessNameGeneratorInterface;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\SiteFactory\Service\PublicAccessServiceInterface;
use Ibexa\Contracts\SiteFactory\Service\SiteServiceInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\SiteFactory\SiteAccessProvider;

final class SiteAccessNameGenerator implements SiteAccessNameGeneratorInterface
{
    /** @var \Ibexa\AdminUi\Siteaccess\SiteAccessNameGeneratorInterface */
    private $inner;

    /** @var \Ibexa\Contracts\SiteFactory\Service\PublicAccessServiceInterface */
    private $publicAccessService;

    /** @var \Ibexa\Contracts\SiteFactory\Service\SiteServiceInterface */
    private $siteService;

    public function __construct(
        SiteAccessNameGeneratorInterface $inner,
        PublicAccessServiceInterface $publicAccessService,
        SiteServiceInterface $siteService
    ) {
        $this->inner = $inner;
        $this->publicAccessService = $publicAccessService;
        $this->siteService = $siteService;
    }

    public function generate(SiteAccess $siteAccess): string
    {
        if ($siteAccess->provider !== SiteAccessProvider::class) {
            return $this->inner->generate($siteAccess);
        }

        $publicAccess = $this->publicAccessService->loadPublicAccess($siteAccess->name);
        if ($publicAccess === null) {
            return $this->inner->generate($siteAccess);
        }

        try {
            $site = $this->siteService->loadSite($publicAccess->getSiteId());
        } catch (NotFoundException $e) {
            return $this->inner->generate($siteAccess);
        }

        return sprintf(
            '%s %s %s (%s)',
            $site->name,
            $publicAccess->getMatcherConfiguration()->host,
            $publicAccess->getMatcherConfiguration()->path,
            $publicAccess->identifier
        );
    }
}

class_alias(SiteAccessNameGenerator::class, 'EzSystems\EzPlatformSiteFactory\SiteAccess\SiteAccessNameGenerator');
