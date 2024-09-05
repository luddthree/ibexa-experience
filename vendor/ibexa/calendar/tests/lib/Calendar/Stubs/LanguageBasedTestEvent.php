<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Calendar\Calendar\Stubs;

use DateTimeInterface;
use Ibexa\Contracts\Calendar\EventType\EventTypeInterface;
use Ibexa\Contracts\Calendar\LanguageBasedEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;

final class LanguageBasedTestEvent extends TestEvent implements LanguageBasedEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language */
    private $language;

    public function __construct(EventTypeInterface $type, string $id, DateTimeInterface $dateTime, Language $language)
    {
        parent::__construct($type, $id, $dateTime);

        $this->language = $language;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }
}

class_alias(LanguageBasedTestEvent::class, 'EzSystems\EzPlatformCalendar\Tests\Calendar\Stubs\LanguageBasedTestEvent');
