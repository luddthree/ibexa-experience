<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\PageBuilder\Timeline;

use Ibexa\Contracts\PageBuilder\PageBuilder\Timeline\BaseEvent;

class BasicEvent extends BaseEvent
{
    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return 'basic';
    }
}

class_alias(BasicEvent::class, 'EzSystems\EzPlatformPageBuilder\PageBuilder\Timeline\BasicEvent');
