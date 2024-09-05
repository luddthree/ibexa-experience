<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Taxonomy\Search\Query\Aggregation;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;

final class TaxonomyEntryIdAggregation extends Aggregation\AbstractTermAggregation
{
    private string $taxonomyIdentifier;

    public function __construct(string $name, string $taxonomyIdentifier)
    {
        parent::__construct($name);
        $this->taxonomyIdentifier = $taxonomyIdentifier;
    }

    public function getTaxonomyIdentifier(): string
    {
        return $this->taxonomyIdentifier;
    }
}
