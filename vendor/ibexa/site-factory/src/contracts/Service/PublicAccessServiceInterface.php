<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\SiteFactory\Service;

use Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess;
use Ibexa\Contracts\SiteFactory\Values\Site\PublicAccessList;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteQuery;

interface PublicAccessServiceInterface
{
    public function loadPublicAccess(string $identifier): ?PublicAccess;

    public function loadPublicAccesses(SiteQuery $query = null): PublicAccessList;
}

class_alias(PublicAccessServiceInterface::class, 'EzSystems\EzPlatformSiteFactory\Service\PublicAccessServiceInterface');
