<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\REST\Server\Input\Parser;

use Ibexa\Bundle\Calendar\REST\Input\Parser\EventActionParser;
use Ibexa\Contracts\Calendar\EventAction\EventActionContext;
use Ibexa\Contracts\Rest\Exceptions;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Scheduler\Calendar\EventAction\UnscheduleEventActionContext;

final class UnscheduleEventActionContextParser extends EventActionParser
{
    protected function parseContext(array $data, ParsingDispatcher $parsingDispatcher): EventActionContext
    {
        if (!isset($data['events'])) {
            throw new Exceptions\Parser("Missing the required 'events' element in data for UnscheduleEventActionContext");
        }

        return new UnscheduleEventActionContext(
            $this->calendarService->loadEvents($data['events'])
        );
    }
}

class_alias(UnscheduleEventActionContextParser::class, 'EzSystems\DateBasedPublisher\Core\REST\Server\Input\Parser\UnscheduleEventActionContextParser');
