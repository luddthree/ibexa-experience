<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Taxonomy\Event;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;

final class MoveTaxonomyEntryRelativeToSiblingEvent extends AfterEvent
{
    private TaxonomyEntry $taxonomyEntry;

    private TaxonomyEntry $sibling;

    /** @phpstan-var \Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface::MOVE_POSITION_* */
    private string $position;

    /**
     * @phpstan-param \Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface::MOVE_POSITION_* $position
     */
    public function __construct(
        TaxonomyEntry $taxonomyEntry,
        TaxonomyEntry $sibling,
        string $position
    ) {
        $this->taxonomyEntry = $taxonomyEntry;
        $this->sibling = $sibling;
        $this->position = $position;
    }

    public function getTaxonomyEntry(): TaxonomyEntry
    {
        return $this->taxonomyEntry;
    }

    public function getSibling(): TaxonomyEntry
    {
        return $this->sibling;
    }

    public function getPosition(): string
    {
        return $this->position;
    }
}
