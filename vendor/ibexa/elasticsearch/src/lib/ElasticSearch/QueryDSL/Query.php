<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\QueryDSL;

interface Query
{
    public function toArray(): array;
}

class_alias(Query::class, 'Ibexa\Platform\ElasticSearchEngine\ElasticSearch\QueryDSL\Query');
