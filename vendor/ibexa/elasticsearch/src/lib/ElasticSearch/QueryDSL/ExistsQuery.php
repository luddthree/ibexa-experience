<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\QueryDSL;

/**
 * Returns documents that contain an indexed value for a field.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-exists-query.html
 */
final class ExistsQuery implements Query
{
    /** @var string */
    private $field;

    public function __construct(string $field)
    {
        $this->field = $field;
    }

    public function toArray(): array
    {
        return [
            'exists' => [
                'field' => $this->field,
            ],
        ];
    }
}

class_alias(ExistsQuery::class, 'Ibexa\Platform\ElasticSearchEngine\ElasticSearch\QueryDSL\ExistsQuery');
