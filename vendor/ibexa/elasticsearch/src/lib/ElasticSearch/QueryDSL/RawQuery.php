<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\QueryDSL;

final class RawQuery implements Query
{
    /** @var array */
    private $query;

    public function __construct(array $query)
    {
        $this->query = $query;
    }

    public function toArray(): array
    {
        return $this->query;
    }
}

class_alias(RawQuery::class, 'Ibexa\Platform\ElasticSearchEngine\ElasticSearch\QueryDSL\RawQuery');
