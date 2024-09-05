<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\Block\QuickActions;

/**
 * @internal
 *
 * @phpstan-import-type THrefActionArray from \Ibexa\Contracts\Dashboard\Block\QuickActions\ActionInterface
 * @phpstan-import-type TUDWActionArray from \Ibexa\Contracts\Dashboard\Block\QuickActions\ActionInterface
 */
interface ConfigurationProviderInterface
{
    /**
     * @return array{name: string, icon_name: string, action: THrefActionArray|TUDWActionArray}
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function getConfiguration(string $actionIdentifier): array;
}
