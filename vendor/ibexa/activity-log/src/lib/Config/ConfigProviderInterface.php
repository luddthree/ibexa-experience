<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\Config;

interface ConfigProviderInterface
{
    /**
     * Return the repository configuration state for activity log enabled state.
     */
    public function isEnabled(): bool;

    public function getTruncateAfterDays(): int;
}
