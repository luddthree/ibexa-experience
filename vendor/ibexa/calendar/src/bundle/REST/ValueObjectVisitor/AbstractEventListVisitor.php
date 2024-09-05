<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Calendar\REST\ValueObjectVisitor;

use Ibexa\Contracts\Calendar\EventList;
use Ibexa\Contracts\Calendar\EventQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

abstract class AbstractEventListVisitor extends ValueObjectVisitor
{
    protected function visitTotalCount(Generator $generator, EventList $data): void
    {
        $generator->startValueElement('totalCount', $data->getTotalCount());
        $generator->endValueElement('totalCount');
    }

    protected function visitEvents(Visitor $visitor, Generator $generator, EventList $data): void
    {
        $generator->startList('events');
        foreach ($data->getEvents() as $event) {
            $visitor->visitValueObject($event);
        }
        $generator->endList('events');
    }

    protected function generateCurrentPageUrlAttribute(Generator $generator, EventList $eventList): void
    {
        $currentPageUrl = $this->getEventListUrl($eventList->getQuery());

        $generator->startAttribute('currentPage', $currentPageUrl);
        $generator->endAttribute('currentPage');
    }

    protected function generateNextPageUrlAttribute(Generator $generator, EventList $eventList): void
    {
        $nextPageQuery = $eventList->getNextPageQuery();
        if ($nextPageQuery !== null) {
            $nextPageUrl = $this->getEventListUrl($nextPageQuery);

            $generator->startAttribute('nextPage', $nextPageUrl);
            $generator->endAttribute('nextPage');
        }
    }

    private function getEventListUrl(EventQuery $query): string
    {
        $parameters = [
            'start' => $query->getDateRange()->getStartDate()->format('U'),
            'end' => $query->getDateRange()->getEndDate()->format('U'),
            'count' => $query->getCount(),
        ];

        if (!empty($query->getTypes())) {
            $parameters['types'] = implode(',', $query->getTypes());
        }

        if (!empty($query->getLanguages())) {
            $parameters['languages'] = implode(',', array_map(static function (Language $language) {
                return $language->languageCode;
            }, $query->getLanguages()));
        }

        if ($query->getCursor() !== null) {
            $parameters['cursor'] = (string)$query->getCursor();
        }

        return $this->router->generate('ibexa.calendar.rest.event.list', $parameters);
    }
}

class_alias(AbstractEventListVisitor::class, 'EzSystems\EzPlatformCalendarBundle\REST\ValueObjectVisitor\AbstractEventListVisitor');
