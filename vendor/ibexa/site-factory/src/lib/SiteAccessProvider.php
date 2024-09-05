<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory;

use Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessProviderInterface;
use Ibexa\Core\MVC\Symfony\SiteAccessGroup;
use Ibexa\SiteFactory\Exception\DataReadException;
use Ibexa\SiteFactory\Service\PublicAccessService;
use Traversable;

class SiteAccessProvider implements SiteAccessProviderInterface
{
    /** @var \Ibexa\SiteFactory\Service\PublicAccessService */
    private $publicAccessService;

    public function __construct(PublicAccessService $publicAccessService)
    {
        $this->publicAccessService = $publicAccessService;
    }

    public function isDefined(?string $name): bool
    {
        try {
            return $this->publicAccessService->loadPublicAccess($name) !== null;
        } catch (DataReadException $e) {
            // when Public Access can't be fetched from database in case of connection error then we assume that it is not defined
            @trigger_error(
                sprintf(
                    'Public Access with name "%s" can\'t be fetched from the database.
                    Please check your connection parameters and database schema.',
                    $name
                ),
                E_USER_WARNING
            );

            return false;
        }
    }

    public function getSiteAccess(string $name): SiteAccess
    {
        $publicAccess = $this->publicAccessService->loadPublicAccess($name);

        if ($publicAccess === null) {
            throw new \RuntimeException("Undefined siteaccess: $name");
        }

        return $this->mapPublicAccess($publicAccess);
    }

    public function getSiteAccesses(): Traversable
    {
        $publicAccesses = $this->publicAccessService->loadPublicAccesses();

        foreach ($publicAccesses as $publicAccess) {
            yield $this->mapPublicAccess($publicAccess);
        }

        yield from [];
    }

    private function mapPublicAccess(PublicAccess $publicAccess): SiteAccess
    {
        $siteAccess = new SiteAccess($publicAccess->identifier);
        $siteAccess->provider = self::class;
        $siteAccess->groups = [new SiteAccessGroup($publicAccess->saGroup)];

        return $siteAccess;
    }
}

class_alias(SiteAccessProvider::class, 'EzSystems\EzPlatformSiteFactory\SiteAccessProvider');
