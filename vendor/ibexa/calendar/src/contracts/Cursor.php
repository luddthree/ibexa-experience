<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Calendar;

use DateTimeImmutable;
use DateTimeInterface;

/**
 * Class represents pagination cursor.
 */
final class Cursor
{
    private const DELIMITER = ':';

    /** @var \DateTimeInterface */
    private $dateTime;

    /** @var string */
    private $type;

    /** @var string */
    private $id;

    private function __construct(DateTimeInterface $dateTime, string $type, string $id)
    {
        if (!($dateTime instanceof DateTimeImmutable)) {
            $dateTime = DateTimeImmutable::createFromFormat('U', (string)$dateTime->getTimestamp(), $dateTime->getTimezone());
        }

        $this->type = $type;
        $this->id = $id;
        $this->dateTime = $dateTime;
    }

    public function getDateTime(): DateTimeInterface
    {
        return $this->dateTime;
    }

    public function getEventId(): string
    {
        return $this->id;
    }

    public function getEventType(): string
    {
        return $this->type;
    }

    public function __toString(): string
    {
        return base64_encode(implode(self::DELIMITER, [
            $this->dateTime->getTimestamp(),
            $this->type,
            $this->id,
        ]));
    }

    /**
     * Creates cursor from given $event.
     *
     * @return \Ibexa\Contracts\Calendar\Cursor
     */
    public static function fromEvent(Event $event): self
    {
        return new self(
            $event->getDateTime(),
            $event->getType()->getTypeIdentifier(),
            $event->getId()
        );
    }

    /**
     * Creates cursor from given $string.
     *
     * @return \Ibexa\Contracts\Calendar\Cursor
     */
    public static function fromString(string $string): self
    {
        list($timestamp, $type, $id) = explode(self::DELIMITER, base64_decode($string), 3);

        return new self(new DateTimeImmutable("@$timestamp"), $type, $id);
    }
}

class_alias(Cursor::class, 'EzSystems\EzPlatformCalendar\Calendar\Cursor');
