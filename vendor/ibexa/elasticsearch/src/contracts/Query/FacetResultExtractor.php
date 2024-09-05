<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Elasticsearch\Query;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\FacetBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\Facet;

/**
 * @deprecated since eZ Platform 3.2.0, to be removed in Ibexa 4.0.0.
 */
interface FacetResultExtractor
{
    public function supports(FacetBuilder $builder): bool;

    public function extract(FacetBuilder $builder, array $data): Facet;
}

class_alias(FacetResultExtractor::class, 'Ibexa\Platform\Contracts\ElasticSearchEngine\Query\FacetResultExtractor');
