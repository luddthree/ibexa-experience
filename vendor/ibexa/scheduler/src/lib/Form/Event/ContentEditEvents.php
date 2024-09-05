<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Form\Event;

final class ContentEditEvents
{
    /**
     * Triggered when scheduling content to be published later.
     */
    public const CONTENT_SCHEDULE_PUBLISH = 'content.edit.schedule_publish';

    /**
     * Triggered when discarding schedule to be published later order.
     */
    public const CONTENT_DISCARD_SCHEDULE_PUBLISH = 'content.edit.discard_schedule_publish';
}

class_alias(ContentEditEvents::class, 'EzSystems\DateBasedPublisher\Core\Form\Event\ContentEditEvents');
