<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ActivityLog\Exception;

use DomainException;
use Ibexa\Contracts\Core\Repository\Exceptions\Exception;

final class ContextLockedException extends DomainException implements Exception
{
}
