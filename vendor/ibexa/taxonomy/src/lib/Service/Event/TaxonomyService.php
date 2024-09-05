<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Service\Event;

use Ibexa\Contracts\Taxonomy\Event\BeforeCreateTaxonomyEntryEvent;
use Ibexa\Contracts\Taxonomy\Event\BeforeMoveTaxonomyEntryEvent;
use Ibexa\Contracts\Taxonomy\Event\BeforeMoveTaxonomyEntryRelativeToSiblingEvent;
use Ibexa\Contracts\Taxonomy\Event\BeforeRemoveTaxonomyEntryEvent;
use Ibexa\Contracts\Taxonomy\Event\BeforeUpdateTaxonomyEntryEvent;
use Ibexa\Contracts\Taxonomy\Event\CreateTaxonomyEntryEvent;
use Ibexa\Contracts\Taxonomy\Event\MoveTaxonomyEntryEvent;
use Ibexa\Contracts\Taxonomy\Event\MoveTaxonomyEntryRelativeToSiblingEvent;
use Ibexa\Contracts\Taxonomy\Event\RemoveTaxonomyEntryEvent;
use Ibexa\Contracts\Taxonomy\Event\UpdateTaxonomyEntryEvent;
use Ibexa\Contracts\Taxonomy\Service\Decorator\TaxonomyServiceDecorator;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryCreateStruct;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryUpdateStruct;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class TaxonomyService extends TaxonomyServiceDecorator
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        TaxonomyServiceInterface $innerService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($innerService);

        $this->eventDispatcher = $eventDispatcher;
    }

    public function createEntry(TaxonomyEntryCreateStruct $createStruct): TaxonomyEntry
    {
        $eventData = [
            $createStruct,
        ];

        $beforeEvent = new BeforeCreateTaxonomyEntryEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getTaxonomyEntry();
        }

        $taxonomyEntry = $beforeEvent->hasTaxonomyEntry()
            ? $beforeEvent->getTaxonomyEntry()
            : $this->innerService->createEntry($createStruct);

        $this->eventDispatcher->dispatch(
            new CreateTaxonomyEntryEvent($taxonomyEntry, ...$eventData)
        );

        return $taxonomyEntry;
    }

    public function updateEntry(TaxonomyEntry $taxonomyEntry, TaxonomyEntryUpdateStruct $updateStruct): TaxonomyEntry
    {
        $eventData = [
            $taxonomyEntry,
            $updateStruct,
        ];

        $beforeEvent = new BeforeUpdateTaxonomyEntryEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getUpdatedTaxonomyEntry();
        }

        $updatedTaxonomyEntry = $beforeEvent->hasUpdatedTaxonomyEntry()
            ? $beforeEvent->getUpdatedTaxonomyEntry()
            : $this->innerService->updateEntry($taxonomyEntry, $updateStruct);

        $this->eventDispatcher->dispatch(
            new UpdateTaxonomyEntryEvent($updatedTaxonomyEntry, ...$eventData)
        );

        return $updatedTaxonomyEntry;
    }

    public function removeEntry(TaxonomyEntry $entry): void
    {
        $beforeEvent = new BeforeRemoveTaxonomyEntryEvent($entry);

        $beforeEvent = $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->removeEntry($entry);

        $this->eventDispatcher->dispatch(
            new RemoveTaxonomyEntryEvent($entry, $beforeEvent->getContentIds())
        );
    }

    public function moveEntryRelativeToSibling(TaxonomyEntry $entry, TaxonomyEntry $sibling, string $position): void
    {
        $beforeEvent = new BeforeMoveTaxonomyEntryRelativeToSiblingEvent($entry, $sibling, $position);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->moveEntryRelativeToSibling($entry, $sibling, $position);

        $this->eventDispatcher->dispatch(
            new MoveTaxonomyEntryRelativeToSiblingEvent($entry, $sibling, $position)
        );
    }

    public function moveEntry(TaxonomyEntry $entry, TaxonomyEntry $newParent): void
    {
        $beforeEvent = new BeforeMoveTaxonomyEntryEvent($entry, $newParent);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->moveEntry($entry, $newParent);

        $this->eventDispatcher->dispatch(
            new MoveTaxonomyEntryEvent($entry, $newParent)
        );
    }
}
