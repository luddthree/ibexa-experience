<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Taxonomy\Service;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryAssignment;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryAssignmentCollection;

interface TaxonomyEntryAssignmentServiceInterface
{
    /**
     * @throws \Ibexa\Taxonomy\Exception\TaxonomyEntryAssignmentNotFoundException
     */
    public function loadAssignmentById(int $id): TaxonomyEntryAssignment;

    public function loadAssignments(Content $content, ?int $versionNo = null): TaxonomyEntryAssignmentCollection;

    /**
     * @throws \Ibexa\Taxonomy\Exception\TaxonomyEntryNotFoundException
     */
    public function assignToContent(Content $content, TaxonomyEntry $entry, ?int $versionNo = null): void;

    /**
     * @throws \Ibexa\Taxonomy\Exception\TaxonomyEntryAssignmentNotFoundException
     */
    public function unassignFromContent(Content $content, TaxonomyEntry $entry, ?int $versionNo = null): void;

    /**
     * @param \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry[] $entries
     */
    public function assignMultipleToContent(Content $content, array $entries, ?int $versionNo = null): void;

    /**
     * @param \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry[] $entries
     */
    public function unassignMultipleFromContent(Content $content, array $entries, ?int $versionNo = null): void;
}
