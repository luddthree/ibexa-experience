<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Calendar\REST\Input\Parser;

use Ibexa\Bundle\Calendar\REST\Values\EventActionInput;
use Ibexa\Contracts\Calendar\CalendarServiceInterface;
use Ibexa\Contracts\Calendar\EventAction\EventActionContext;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;

abstract class EventActionParser extends BaseParser
{
    /** @var \Ibexa\Contracts\Calendar\CalendarServiceInterface */
    protected $calendarService;

    public function __construct(CalendarServiceInterface $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    final public function parse(array $data, ParsingDispatcher $parsingDispatcher): ValueObject
    {
        return new EventActionInput([
            'context' => $this->parseContext($data, $parsingDispatcher),
        ]);
    }

    abstract protected function parseContext(array $data, ParsingDispatcher $parsingDispatcher): EventActionContext;
}

class_alias(EventActionParser::class, 'EzSystems\EzPlatformCalendarBundle\REST\Input\Parser\EventActionParser');
