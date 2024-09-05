<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\GraphQL\Schema\Domain\Iterator;

use Generator;
use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Domain\Iterator;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;

/**
 * @internal
 */
final class TaxonomyIterator implements Iterator
{
    private TaxonomyConfiguration $taxonomyConfiguration;

    /** @var array<string> */
    private array $taxonomies;

    public function __construct(TaxonomyConfiguration $taxonomyConfiguration)
    {
        $this->taxonomyConfiguration = $taxonomyConfiguration;
    }

    public function init(Builder $schema): void
    {
        $this->taxonomies = $this->taxonomyConfiguration->getTaxonomies();
    }

    public function iterate(): Generator
    {
        foreach ($this->taxonomies as $taxonomy) {
            yield ['Taxonomy' => $taxonomy];
        }
    }
}
