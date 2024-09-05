<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\View\Matcher\TaxonomyEntryBased;

use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;

final class Id extends AbstractMatcher
{
    /** @var int[] */
    private array $values = [];

    protected function matchTaxonomyEntry(TaxonomyEntry $entry): bool
    {
        return in_array($entry->id, $this->values, true);
    }

    public function setMatchingConfig($matchingConfig): void
    {
        $this->values = !is_array($matchingConfig) ? [$matchingConfig] : $matchingConfig;
    }
}
