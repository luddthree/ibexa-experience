<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Config\Repository;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;

/**
 * @internal
 */
final class RepositoryConfigResolver implements RepositoryConfigResolverInterface
{
    private const USE_CONTENT_REMOTE_ID_PARAMETER = 'personalization.repository.content.use_remote_id';

    private ConfigResolverInterface $configResolver;

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    public function useRemoteId(): bool
    {
        if (!$this->configResolver->hasParameter(self::USE_CONTENT_REMOTE_ID_PARAMETER)) {
            return false;
        }

        return $this->configResolver->getParameter(self::USE_CONTENT_REMOTE_ID_PARAMETER);
    }
}
