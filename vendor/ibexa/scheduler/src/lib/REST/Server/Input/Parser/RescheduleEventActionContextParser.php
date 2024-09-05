<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\REST\Server\Input\Parser;

use DateTimeImmutable;
use Exception;
use Ibexa\Bundle\Calendar\REST\Input\Parser\EventActionParser;
use Ibexa\Contracts\Calendar\EventAction\EventActionContext;
use Ibexa\Contracts\Rest\Exceptions;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Scheduler\Calendar\EventAction\RescheduleEventActionContext;

final class RescheduleEventActionContextParser extends EventActionParser
{
    protected function parseContext(array $data, ParsingDispatcher $parsingDispatcher): EventActionContext
    {
        if (!isset($data['events'])) {
            throw new Exceptions\Parser("Missing the required 'events' element in data for RescheduleEventActionContext");
        }

        if (!isset($data['dateTime'])) {
            throw new Exceptions\Parser("Missing the required 'dateTime' element in data for RescheduleEventActionContext");
        }

        return new RescheduleEventActionContext(
            $this->calendarService->loadEvents($data['events']),
            $this->parseDateTimeValue($data['dateTime'])
        );
    }

    private function parseDateTimeValue($value): DateTimeImmutable
    {
        try {
            if (is_numeric($value)) {
                $value = '@' . $value;
            }

            return new DateTimeImmutable($value);
        } catch (Exception $e) {
            throw new Exceptions\Parser("Invalid 'dateTime' value format");
        }
    }
}

class_alias(RescheduleEventActionContextParser::class, 'EzSystems\DateBasedPublisher\Core\REST\Server\Input\Parser\RescheduleEventActionContextParser');
