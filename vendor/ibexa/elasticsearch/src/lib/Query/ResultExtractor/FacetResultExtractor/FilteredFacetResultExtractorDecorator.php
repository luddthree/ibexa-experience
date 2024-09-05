<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\ResultExtractor\FacetResultExtractor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\FacetBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\Facet;
use Ibexa\Contracts\Elasticsearch\Query\FacetResultExtractor;
use Ibexa\Elasticsearch\Query\FacetBuilderVisitor\FilteredFacetVisitorDecorator;

/**
 * @deprecated since eZ Platform 3.2.0, to be removed in Ibexa 4.0.0.
 */
final class FilteredFacetResultExtractorDecorator implements FacetResultExtractor
{
    /** @var \Ibexa\Contracts\Elasticsearch\Query\FacetResultExtractor */
    private $innerResultExtractor;

    public function __construct(FacetResultExtractor $innerResultExtractor)
    {
        $this->innerResultExtractor = $innerResultExtractor;
    }

    public function supports(FacetBuilder $builder): bool
    {
        return $this->innerResultExtractor->supports($builder);
    }

    public function extract(FacetBuilder $builder, array $data): Facet
    {
        if ($builder->filter instanceof Criterion) {
            $data = $data[FilteredFacetVisitorDecorator::INNER_AGGREGATION_KEY];
        }

        return $this->innerResultExtractor->extract($builder, $data);
    }
}

class_alias(FilteredFacetResultExtractorDecorator::class, 'Ibexa\Platform\ElasticSearchEngine\Query\ResultExtractor\FacetResultExtractor\FilteredFacetResultExtractorDecorator');
