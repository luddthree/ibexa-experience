<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\REST\Input\Value;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * @property-read \Ibexa\Taxonomy\REST\Input\Value\TaxonomyEntryMove[] $entries
 */
final class TaxonomyEntryBulkMove extends ValueObject
{
    /** @var \Ibexa\Taxonomy\REST\Input\Value\TaxonomyEntryMove[] */
    protected array $entries;

    /**
     * @param \Ibexa\Taxonomy\REST\Input\Value\TaxonomyEntryMove[] $entries
     */
    public function __construct(
        array $entries
    ) {
        parent::__construct([
            'entries' => $entries,
        ]);
    }

    /**
     * @return \Ibexa\Taxonomy\REST\Input\Value\TaxonomyEntryMove[]
     */
    public function getEntries(): array
    {
        return $this->entries;
    }
}
