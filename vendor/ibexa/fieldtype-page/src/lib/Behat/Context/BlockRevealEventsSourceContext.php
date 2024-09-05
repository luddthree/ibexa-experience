<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Behat\Context;

use Ibexa\Contracts\Calendar\Event;
use Ibexa\FieldTypePage\Calendar\BlockRevealEventSource;
use Ibexa\FieldTypePage\Calendar\BlockRevealEventType;
use PHPUnit\Framework\Assert;

class BlockRevealEventsSourceContext extends BlockEventsSourceContext
{
    /** @var \Ibexa\FieldTypePage\Calendar\BlockRevealEventSource */
    private $blockRevealEventSource;

    /** @var \Ibexa\FieldTypePage\Calendar\BlockRevealEventType */
    private $blockRevealEventType;

    /** @var \Ibexa\Contracts\Calendar\Event[] */
    private $fetchedRevealEvents;

    /** @var int */
    private $countedRevealEvents;

    /** @var \Ibexa\Contracts\Calendar\Event */
    private $revealEventFetchedById;

    /** @var \Ibexa\Contracts\Calendar\Event */
    private $revealEventFetchedByName;

    public function __construct(
        BlockRevealEventSource $blockRevealEventSource,
        BlockRevealEventType $blockRevealEventType
    ) {
        $this->blockRevealEventSource = $blockRevealEventSource;
        $this->blockRevealEventType = $blockRevealEventType;
    }

    /**
     * @When I ask for next month block reveal events
     */
    public function iAskForNextMonthBlockRevealEvents()
    {
        $query = $this->buildEventQuery($this->blockRevealEventType->getTypeIdentifier());

        $this->fetchedRevealEvents = $this->blockRevealEventSource->getEvents($query);
    }

    /**
     * @Then I receive :quantity reveal event for :name block
     */
    public function iReceiveRevealEventForBlock(int $quantity, string $name)
    {
        $events = $this->fetchEventsByName($name);

        Assert::assertEquals($quantity, count($events));
        Assert::assertEquals($quantity, $this->countedRevealEvents);
    }

    /**
     * @When I ask for quantity of next month block reveal events
     */
    public function iAskForQuantityOfNextMonthBlockRevealEvents()
    {
        $query = $this->buildEventQuery($this->blockRevealEventType->getTypeIdentifier());

        $this->countedRevealEvents = $this->blockRevealEventSource->getCount($query);
    }

    /**
     * @return \Ibexa\FieldTypePage\Calendar\BlockRevealEvent[]
     */
    private function fetchEventsByName(string $name): array
    {
        return array_filter($this->fetchedRevealEvents, static function (Event $event) use ($name) {
            return $event->getName() === $name;
        });
    }

    /**
     * @When I ask for reveal event with name :name by its ID
     */
    public function iAskForRevealEventWithNameByItsId(string $name)
    {
        $this->iAskForNextMonthBlockRevealEvents();

        $eventsFetchedByName = $this->fetchEventsByName($name);
        $this->revealEventFetchedByName = reset($eventsFetchedByName);

        $eventsFetchedById = $this->blockRevealEventSource->loadEvents([
            $this->revealEventFetchedByName->getId(),
        ]);
        $this->revealEventFetchedById = reset($eventsFetchedById);
    }

    /**
     * @Then I receive proper block reveal event
     */
    public function iReceiveProperBlockRevealEvent()
    {
        Assert::assertEquals(
            $this->revealEventFetchedByName->getId(),
            $this->revealEventFetchedById->getId()
        );
    }
}
