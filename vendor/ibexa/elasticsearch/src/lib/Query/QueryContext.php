<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;

final class QueryContext
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Query */
    private $query;

    /** @var \Ibexa\Contracts\Elasticsearch\Query\LanguageFilter */
    private $languageFilter;

    public function __construct(Query $query, LanguageFilter $languageFilter)
    {
        $this->query = $query;
        $this->languageFilter = $languageFilter;
    }

    public function getQuery(): Query
    {
        return $this->query;
    }

    public function getLanguageFilter(): LanguageFilter
    {
        return $this->languageFilter;
    }
}

class_alias(QueryContext::class, 'Ibexa\Platform\ElasticSearchEngine\Query\QueryContext');
