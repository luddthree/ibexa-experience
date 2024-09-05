<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Calendar;

use Ibexa\Contracts\Core\Repository\Values\Content\Language;

/**
 * This interface should be implemented by events related to a specific language. For example: scheduled publication of
 * a content translation.
 */
interface LanguageBasedEvent
{
    public function getLanguage(): Language;
}

class_alias(LanguageBasedEvent::class, 'EzSystems\EzPlatformCalendar\Calendar\LanguageBasedEvent');
