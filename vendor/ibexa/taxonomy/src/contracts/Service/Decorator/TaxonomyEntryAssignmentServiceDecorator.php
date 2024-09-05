<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Taxonomy\Service\Decorator;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyEntryAssignmentServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryAssignment;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryAssignmentCollection;

abstract class TaxonomyEntryAssignmentServiceDecorator implements TaxonomyEntryAssignmentServiceInterface
{
    protected TaxonomyEntryAssignmentServiceInterface $innerService;

    public function __construct(TaxonomyEntryAssignmentServiceInterface $innerService)
    {
        $this->innerService = $innerService;
    }

    public function loadAssignmentById(int $id): TaxonomyEntryAssignment
    {
        return $this->innerService->loadAssignmentById($id);
    }

    public function loadAssignments(Content $content, ?int $versionNo = null): TaxonomyEntryAssignmentCollection
    {
        return $this->innerService->loadAssignments($content, $versionNo);
    }

    public function assignToContent(Content $content, TaxonomyEntry $entry, ?int $versionNo = null): void
    {
        $this->innerService->assignToContent($content, $entry, $versionNo);
    }

    public function unassignFromContent(Content $content, TaxonomyEntry $entry, ?int $versionNo = null): void
    {
        $this->innerService->unassignFromContent($content, $entry, $versionNo);
    }

    public function assignMultipleToContent(Content $content, array $entries, ?int $versionNo = null): void
    {
        $this->innerService->assignMultipleToContent($content, $entries, $versionNo);
    }

    public function unassignMultipleFromContent(Content $content, array $entries, ?int $versionNo = null): void
    {
        $this->innerService->unassignMultipleFromContent($content, $entries, $versionNo);
    }
}
