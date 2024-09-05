<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ActivityLog\DependencyInjection\Configuration\RepositoryAware;

use Ibexa\ActivityLog\Config\ConfigProvider;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\RepositoryConfigParserInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

final class ActivityLogParser implements RepositoryConfigParserInterface
{
    public function addSemanticConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode(ConfigProvider::ACTIVITY_LOG)
                ->canBeDisabled()
                ->children()
                    ->integerNode(ConfigProvider::ACTIVITY_LOG_TRUNCATE_AFTER_DAYS)
                        ->info('The amount of days for activity log after which logs should be considered scheduled for removal')
                        ->defaultValue(30)
                        ->min(0)
                    ->end()
                ->end()
            ->end();
    }
}
