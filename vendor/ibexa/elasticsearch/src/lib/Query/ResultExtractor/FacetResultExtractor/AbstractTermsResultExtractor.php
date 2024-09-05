<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\ResultExtractor\FacetResultExtractor;

use Ibexa\Contracts\Elasticsearch\Query\FacetResultExtractor;

/**
 * @deprecated since eZ Platform 3.2.0, to be removed in Ibexa 4.0.0.
 */
abstract class AbstractTermsResultExtractor implements FacetResultExtractor
{
    protected function extractEntries(array $data): array
    {
        $entries = [];
        foreach ($data['buckets'] as $bucket) {
            $entries[$bucket['key']] = $bucket['doc_count'];
        }

        return $entries;
    }
}

class_alias(AbstractTermsResultExtractor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\ResultExtractor\FacetResultExtractor\AbstractTermsResultExtractor');
