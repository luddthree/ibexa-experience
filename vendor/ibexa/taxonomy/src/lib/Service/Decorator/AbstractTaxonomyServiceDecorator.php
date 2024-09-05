<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Service\Decorator;

use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryCreateStruct;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryUpdateStruct;

abstract class AbstractTaxonomyServiceDecorator implements TaxonomyServiceInterface
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
        if ($taxonomyName === null) {
            return $this->innerService->loadEntryByIdentifier($identifier);
        }

        return $this->innerService->loadEntryByIdentifier($identifier, $taxonomyName);
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
}
