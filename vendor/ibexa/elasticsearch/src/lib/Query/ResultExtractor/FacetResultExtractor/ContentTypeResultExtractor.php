<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\ResultExtractor\FacetResultExtractor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\FacetBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\FacetBuilder\ContentTypeFacetBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\Facet;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\Facet\ContentTypeFacet;

/**
 * @deprecated since eZ Platform 3.2.0, to be removed in Ibexa 4.0.0.
 */
final class ContentTypeResultExtractor extends AbstractTermsResultExtractor
{
    public function supports(FacetBuilder $builder): bool
    {
        return $builder instanceof ContentTypeFacetBuilder;
    }

    public function extract(FacetBuilder $builder, array $data): Facet
    {
        $facet = new ContentTypeFacet();
        $facet->name = $builder->name;
        $facet->entries = $this->extractEntries($data);

        return $facet;
    }
}

class_alias(ContentTypeResultExtractor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\ResultExtractor\FacetResultExtractor\ContentTypeResultExtractor');
