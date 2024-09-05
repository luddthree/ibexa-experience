<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Notification;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry;

/**
 * @internal
 */
interface SenderInterface
{
    public function sendPublishNotifications(
        ScheduledEntry $scheduledEntry,
        ContentInfo $contentInfo
    ): void;

    public function sendHideNotifications(
        ScheduledEntry $scheduledEntry,
        ContentInfo $contentInfo
    ): void;
}

class_alias(SenderInterface::class, 'EzSystems\DateBasedPublisher\SPI\Notification\SenderInterface');
