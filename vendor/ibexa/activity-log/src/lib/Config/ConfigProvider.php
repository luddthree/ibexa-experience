<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\Config;

use Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider;

final class ConfigProvider implements ConfigProviderInterface
{
    public const ACTIVITY_LOG = 'activity_log';

    public const ACTIVITY_LOG_TRUNCATE_AFTER_DAYS = 'truncate_after_days';

    private RepositoryConfigurationProvider $repositoryConfigProvider;

    public function __construct(
        RepositoryConfigurationProvider $repositoryConfigurationProvider
    ) {
        $this->repositoryConfigProvider = $repositoryConfigurationProvider;
    }

    public function isEnabled(): bool
    {
        $repositoryConfig = $this->repositoryConfigProvider->getRepositoryConfig();

        return $repositoryConfig[self::ACTIVITY_LOG]['enabled'];
    }

    public function getTruncateAfterDays(): int
    {
        $repositoryConfig = $this->repositoryConfigProvider->getRepositoryConfig();

        return $repositoryConfig[self::ACTIVITY_LOG][self::ACTIVITY_LOG_TRUNCATE_AFTER_DAYS];
    }
}
