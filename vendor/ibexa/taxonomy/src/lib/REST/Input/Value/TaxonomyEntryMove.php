<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\REST\Input\Value;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;

/**
 * @property-read \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry $entry
 * @property-read \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry $sibling
 * @property-read \Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface::MOVE_POSITION_* $position
 */
final class TaxonomyEntryMove extends ValueObject
{
    protected TaxonomyEntry $entry;

    protected TaxonomyEntry $sibling;

    /** @phpstan-var \Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface::MOVE_POSITION_* */
    protected string $position;

    public function __construct(
        TaxonomyEntry $entry,
        TaxonomyEntry $sibling,
        string $position
    ) {
        parent::__construct([
            'entry' => $entry,
            'sibling' => $sibling,
            'position' => $position,
        ]);
    }

    public function getEntry(): TaxonomyEntry
    {
        return $this->entry;
    }

    public function getSibling(): TaxonomyEntry
    {
        return $this->sibling;
    }

    /**
     * @phpstan-return \Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface::MOVE_POSITION_*
     */
    public function getPosition(): string
    {
        return $this->position;
    }
}
