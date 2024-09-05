<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\View\Matcher\TaxonomyEntryBased;

use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;

final class Taxonomy extends AbstractMatcher
{
    private string $taxonomy;

    protected function matchTaxonomyEntry(TaxonomyEntry $entry): bool
    {
        return $this->taxonomy === $entry->taxonomy;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function setMatchingConfig($matchingConfig): void
    {
        if (!is_string($matchingConfig)) {
            throw new InvalidArgumentException('$matchingConfig', 'Taxonomy name needs to be a string');
        }

        $this->taxonomy = $matchingConfig;
    }
}
