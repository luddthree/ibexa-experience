<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Elasticsearch\Query;

use Elasticsearch\Client;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;

/**
 * @internal
 */
interface CoordinatorInterface
{
    public function execute(Client $client, Query $query, array $languageFilter): SearchResult;
}

class_alias(CoordinatorInterface::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CoordinatorInterface');
