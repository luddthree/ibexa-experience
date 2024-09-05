<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Calendar\Controller\REST;

use Ibexa\Contracts\Calendar\CalendarServiceInterface;
use Ibexa\Contracts\Calendar\EventGroupList;
use Ibexa\Contracts\Calendar\EventList;
use Ibexa\Contracts\Calendar\EventQuery;
use Ibexa\Rest\Message;
use Ibexa\Rest\Server\Controller as BaseController;
use Ibexa\Rest\Server\Values\NoContent;
use Ibexa\Rest\Value;
use Symfony\Component\HttpFoundation\Request;

final class EventController extends BaseController
{
    /** @var \Ibexa\Contracts\Calendar\CalendarServiceInterface */
    private $calendarService;

    public function __construct(CalendarServiceInterface $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    public function listEventsAction(Request $request, EventQuery $query): EventList
    {
        return $this->calendarService->getEvents($query);
    }

    public function listEventsByDayAction(Request $request, EventQuery $query): EventGroupList
    {
        return $this->calendarService->getGroupedEvents($query, CalendarServiceInterface::GROUP_BY_DAY);
    }

    public function executeActionAction(Request $request): Value
    {
        /** @var \Ibexa\Bundle\Calendar\REST\Values\EventActionInput $actionInput */
        $actionInput = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        $this->calendarService->executeAction($actionInput->context);

        return new NoContent();
    }
}

class_alias(EventController::class, 'EzSystems\EzPlatformCalendarBundle\Controller\REST\EventController');
