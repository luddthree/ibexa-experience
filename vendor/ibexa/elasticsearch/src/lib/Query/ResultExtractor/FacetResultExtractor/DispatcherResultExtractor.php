<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\ResultExtractor\FacetResultExtractor;

use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\FacetBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\Facet;
use Ibexa\Contracts\Elasticsearch\Query\FacetResultExtractor;

/**
 * @deprecated since eZ Platform 3.2.0, to be removed in Ibexa 4.0.0.
 */
final class DispatcherResultExtractor implements FacetResultExtractor
{
    /** @var \Ibexa\Contracts\Elasticsearch\Query\FacetResultExtractor[] */
    private $extractors;

    public function __construct(iterable $extractors = [])
    {
        $this->extractors = $extractors;
    }

    public function supports(FacetBuilder $builder): bool
    {
        return $this->findExtractor($builder) !== null;
    }

    public function extract(FacetBuilder $builder, array $data): Facet
    {
        $extractor = $this->findExtractor($builder);

        if ($extractor === null) {
            throw new NotImplementedException(
                'No result extractor available for: ' . get_class($builder)
            );
        }

        return $extractor->extract($builder, $data);
    }

    private function findExtractor(FacetBuilder $builder): ?FacetResultExtractor
    {
        foreach ($this->extractors as $extractor) {
            if ($extractor->supports($builder)) {
                return $extractor;
            }
        }

        return null;
    }
}

class_alias(DispatcherResultExtractor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\ResultExtractor\FacetResultExtractor\DispatcherResultExtractor');
