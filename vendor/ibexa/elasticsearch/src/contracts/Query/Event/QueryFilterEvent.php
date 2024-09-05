<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Elasticsearch\Query\Event;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Symfony\Contracts\EventDispatcher\Event;

final class QueryFilterEvent extends Event
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

class_alias(QueryFilterEvent::class, 'Ibexa\Platform\Contracts\ElasticSearchEngine\Query\Event\QueryFilterEvent');
