<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\REST\Values;

use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Rest\Value;

final class RestTaxonomyEntry extends Value
{
    private ?TaxonomyEntry $taxonomyEntry;

    public function __construct(?TaxonomyEntry $tag)
    {
        $this->taxonomyEntry = $tag;
    }

    public function getTaxonomyEntry(): ?TaxonomyEntry
    {
        return $this->taxonomyEntry;
    }
}
