<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Behat\Context;

use Ibexa\Contracts\Calendar\Event;
use Ibexa\FieldTypePage\Calendar\BlockHideEventSource;
use Ibexa\FieldTypePage\Calendar\BlockHideEventType;
use PHPUnit\Framework\Assert;

class BlockHideEventsSourceContext extends BlockEventsSourceContext
{
    /** @var \Ibexa\FieldTypePage\Calendar\BlockRevealEventSource */
    private $blockHideEventSource;

    /** @var \Ibexa\FieldTypePage\Calendar\BlockRevealEventType */
    private $blockHideEventType;

    /** @var \Ibexa\Contracts\Calendar\Event[] */
    private $fetchedHideEvents;

    /** @var number */
    private $countedHideEvents;

    /** @var \Ibexa\Contracts\Calendar\Event */
    private $hideEventFetchedById;

    /** @var \Ibexa\Contracts\Calendar\Event */
    private $hideEventFetchedByName;

    public function __construct(
        BlockHideEventSource $blockRevealEventSource,
        BlockHideEventType $blockHideEventType
    ) {
        $this->blockHideEventSource = $blockRevealEventSource;
        $this->blockHideEventType = $blockHideEventType;
    }

    /**
     * @When I ask for next month block hide events
     */
    public function iAskForNextMonthBlockHideEvents()
    {
        $query = $this->buildEventQuery($this->blockHideEventType->getTypeIdentifier());

        $this->fetchedHideEvents = $this->blockHideEventSource->getEvents($query);
    }

    /**
     * @Then I receive :quantity hide event for :name block
     */
    public function iReceiveHideEventForBlock(int $quantity, string $name)
    {
        $events = $this->fetchEventsByName($name);

        Assert::assertEquals($quantity, count($events));
        Assert::assertEquals($quantity, $this->countedHideEvents);
    }

    /**
     * @When I ask for quantity of next month block hide events
     */
    public function iAskForQuantityOfNextMonthBlockHideEvents()
    {
        $query = $this->buildEventQuery($this->blockHideEventType->getTypeIdentifier());

        $this->countedHideEvents = $this->blockHideEventSource->getCount($query);
    }

    /**
     * @return \Ibexa\FieldTypePage\Calendar\BlockRevealEvent[]
     */
    private function fetchEventsByName(string $name): array
    {
        return array_filter(
            $this->fetchedHideEvents,
            static function (Event $event) use ($name) {
                return $event->getName() === $name;
            }
        );
    }

    /**
     * @When I ask for hide event with name :name by its ID
     */
    public function iAskForHideEventWithNameByItsId(string $name)
    {
        $this->iAskForNextMonthBlockHideEvents();

        $eventsFetchedByName = $this->fetchEventsByName($name);
        $this->hideEventFetchedByName = reset($eventsFetchedByName);

        $eventsFetchedById = $this->blockHideEventSource->loadEvents([
            $this->hideEventFetchedByName->getId(),
        ]);
        $this->hideEventFetchedById = reset($eventsFetchedById);
    }

    /**
     * @Then I receive proper block hide event
     */
    public function iReceiveProperBlockHideEvent()
    {
        Assert::assertEquals(
            $this->hideEventFetchedByName->getId(),
            $this->hideEventFetchedById->getId()
        );
    }
}
