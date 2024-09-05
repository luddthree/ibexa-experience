<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory\Persistence\Cache;

/**
 * Abstract handler for use in other Persistence Cache Handlers.
 */
abstract class AbstractHandler
{
    final protected function escapeForCacheKey(string $identifier): string
    {
        return \str_replace(
            ['_', '/', ':', '(', ')', '@', '\\', '{', '}'],
            ['__', '_S', '_C', '_BO', '_BC', '_A', '_BS', '_CBO', '_CBC'],
            $identifier
        );
    }
}

class_alias(AbstractHandler::class, 'EzSystems\EzPlatformSiteFactory\Persistence\Cache\AbstractHandler');
