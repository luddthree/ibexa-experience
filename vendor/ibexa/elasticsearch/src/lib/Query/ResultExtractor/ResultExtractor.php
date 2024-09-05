<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\ResultExtractor;

use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Ibexa\Elasticsearch\Query\QueryContext;

interface ResultExtractor
{
    public function extract(QueryContext $queryContext, array $data): SearchResult;

    public function getExpectedSourceFields(): array;
}

class_alias(ResultExtractor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\ResultExtractor\ResultExtractor');
