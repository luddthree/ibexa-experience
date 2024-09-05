<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Result;

final class DiffStatus
{
    public const UNCHANGED = 'unchanged';

    public const ADDED = 'added';

    public const REMOVED = 'removed';
}

class_alias(DiffStatus::class, 'EzSystems\EzPlatformVersionComparison\Result\DiffStatus');
