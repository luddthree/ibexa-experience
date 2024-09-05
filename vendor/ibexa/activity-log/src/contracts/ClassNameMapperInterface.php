<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ActivityLog;

interface ClassNameMapperInterface
{
    /**
     * @phpstan-return iterable<class-string, string>
     */
    public function getClassNameToShortNameMap(): iterable;
}
