<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\Exception;

use RuntimeException;

/**
 * Zone not found exception.
 */
class ZoneNotFoundException extends RuntimeException
{
}

class_alias(ZoneNotFoundException::class, 'EzSystems\EzPlatformPageFieldType\Exception\ZoneNotFoundException');
