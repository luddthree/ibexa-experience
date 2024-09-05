<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\REST\Input\Value;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * @property-read \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry[] $entries
 */
final class TaxonomyEntryBulkRemove extends ValueObject
{
    /** @var \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry[] */
    protected array $entries;

    /**
     * @param \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry[] $entries
     */
    public function __construct(
        array $entries
    ) {
        parent::__construct([
            'entries' => $entries,
        ]);
    }

    /**
     * @return \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry[]
     */
    public function getEntries(): array
    {
        return $this->entries;
    }

    /**
     * @param \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry[] $entries
     */
    public function setEntries(array $entries): void
    {
        $this->entries = $entries;
    }
}
