<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Taxonomy\Service\Decorator;

use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryCreateStruct;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryUpdateStruct;
use Traversable;

abstract class TaxonomyServiceDecorator implements TaxonomyServiceInterface
{
    protected TaxonomyServiceInterface $innerService;

    public function __construct(TaxonomyServiceInterface $innerService)
    {
        $this->innerService = $innerService;
    }

    public function loadEntryById(int $id): TaxonomyEntry
    {
        return $this->innerService->loadEntryById($id);
    }

    public function loadEntryByIdentifier(string $identifier, string $taxonomyName = null): TaxonomyEntry
    {
        return $this->innerService->loadEntryByIdentifier($identifier, $taxonomyName);
    }

    public function loadRootEntry(string $taxonomyName): TaxonomyEntry
    {
        return $this->innerService->loadRootEntry($taxonomyName);
    }

    public function loadEntryByContentId(int $contentId): TaxonomyEntry
    {
        return $this->innerService->loadEntryByContentId($contentId);
    }

    public function createEntry(TaxonomyEntryCreateStruct $createStruct): TaxonomyEntry
    {
        return $this->innerService->createEntry($createStruct);
    }

    public function updateEntry(TaxonomyEntry $taxonomyEntry, TaxonomyEntryUpdateStruct $updateStruct): TaxonomyEntry
    {
        return $this->innerService->updateEntry($taxonomyEntry, $updateStruct);
    }

    public function removeEntry(TaxonomyEntry $entry): void
    {
        $this->innerService->removeEntry($entry);
    }

    public function moveEntry(TaxonomyEntry $entry, TaxonomyEntry $newParent): void
    {
        $this->innerService->moveEntry($entry, $newParent);
    }

    public function moveEntryRelativeToSibling(TaxonomyEntry $entry, TaxonomyEntry $sibling, string $position): void
    {
        $this->innerService->moveEntryRelativeToSibling($entry, $sibling, $position);
    }

    public function loadAllEntries(?string $taxonomyName = null, ?int $limit = 30, int $offset = 0): Traversable
    {
        return $this->innerService->loadAllEntries($taxonomyName, $limit, $offset);
    }

    public function countAllEntries(?string $taxonomyName = null): int
    {
        return $this->innerService->countAllEntries($taxonomyName);
    }

    public function loadEntryChildren(TaxonomyEntry $parentEntry, ?int $limit = 30, int $offset = 0): Traversable
    {
        return $this->innerService->loadEntryChildren($parentEntry, $limit, $offset);
    }

    public function countEntryChildren(TaxonomyEntry $parentEntry): int
    {
        return $this->innerService->countEntryChildren($parentEntry);
    }

    public function getPath(TaxonomyEntry $entry): iterable
    {
        return $this->innerService->getPath($entry);
    }
}
