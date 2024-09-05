<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Taxonomy\Service;

use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryCreateStruct;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryUpdateStruct;
use Traversable;

interface TaxonomyServiceInterface
{
    public const MOVE_POSITION_NEXT = 'next';
    public const MOVE_POSITION_PREV = 'prev';

    /**
     * @throws \Ibexa\Taxonomy\Exception\TaxonomyEntryNotFoundException
     */
    public function loadEntryById(int $id): TaxonomyEntry;

    /**
     * @throws \Ibexa\Taxonomy\Exception\TaxonomyEntryNotFoundException
     */
    public function loadEntryByIdentifier(string $identifier, string $taxonomyName = null): TaxonomyEntry;

    /**
     * @throws \Ibexa\Taxonomy\Exception\TaxonomyEntryNotFoundException
     */
    public function loadRootEntry(string $taxonomyName): TaxonomyEntry;

    /**
     * @throws \Ibexa\Taxonomy\Exception\TaxonomyEntryNotFoundException
     */
    public function loadEntryByContentId(int $contentId): TaxonomyEntry;

    public function createEntry(TaxonomyEntryCreateStruct $createStruct): TaxonomyEntry;

    public function updateEntry(TaxonomyEntry $taxonomyEntry, TaxonomyEntryUpdateStruct $updateStruct): TaxonomyEntry;

    public function removeEntry(TaxonomyEntry $entry): void;

    public function moveEntry(TaxonomyEntry $entry, TaxonomyEntry $newParent): void;

    /**
     * @phpstan-param self::MOVE_POSITION_* $position
     */
    public function moveEntryRelativeToSibling(TaxonomyEntry $entry, TaxonomyEntry $sibling, string $position): void;

    /**
     * @return \Traversable<\Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry>
     */
    public function loadAllEntries(?string $taxonomyName = null, ?int $limit = 30, int $offset = 0): Traversable;

    public function countAllEntries(?string $taxonomyName = null): int;

    /**
     * @return \Traversable<\Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry>
     */
    public function loadEntryChildren(TaxonomyEntry $parentEntry, ?int $limit = 30, int $offset = 0): Traversable;

    public function countEntryChildren(TaxonomyEntry $parentEntry): int;

    /**
     * @return iterable<\Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry>
     */
    public function getPath(TaxonomyEntry $entry): iterable;
}
