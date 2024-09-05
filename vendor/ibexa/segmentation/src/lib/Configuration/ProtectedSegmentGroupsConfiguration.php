<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Configuration;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Segmentation\Configuration\ProtectedSegmentGroupsConfigurationInterface;

final class ProtectedSegmentGroupsConfiguration implements ProtectedSegmentGroupsConfigurationInterface
{
    private ConfigResolverInterface $configResolver;

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    /**
     * @return array<string>
     */
    public function getProtectedGroupIdentifiers(): array
    {
        /** @var array<
         *  array{
         *      protected: bool,
         * }> $groups
         */
        $groups = $this->configResolver->getParameter('segmentation.segment_groups.list');

        return array_keys(
            array_filter(
                $groups,
                /**
                 * @param array{protected: bool} $group
                 */
                static function (array $group): bool {
                    return $group['protected'];
                }
            )
        );
    }
}
