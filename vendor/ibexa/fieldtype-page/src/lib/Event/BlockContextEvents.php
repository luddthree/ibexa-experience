<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Event;

final class BlockContextEvents
{
    public const CREATE = 'ezplatform.ezlandingpage.block.context.create';
}

class_alias(BlockContextEvents::class, 'EzSystems\EzPlatformPageFieldType\Event\BlockContextEvents');
