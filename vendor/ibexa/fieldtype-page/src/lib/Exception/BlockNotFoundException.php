<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\Exception;

use RuntimeException;

/**
 * Block not found exception.
 */
class BlockNotFoundException extends RuntimeException
{
}

class_alias(BlockNotFoundException::class, 'EzSystems\EzPlatformPageFieldType\Exception\BlockNotFoundException');
