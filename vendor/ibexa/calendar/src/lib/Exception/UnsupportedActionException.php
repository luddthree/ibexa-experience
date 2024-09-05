<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Calendar\Exception;

use RuntimeException;

final class UnsupportedActionException extends RuntimeException
{
}

class_alias(UnsupportedActionException::class, 'EzSystems\EzPlatformCalendar\Calendar\Exception\UnsupportedActionException');
