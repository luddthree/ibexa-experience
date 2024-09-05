<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\QueryDSL;

final class Range
{
    /** @var string|null */
    private $key;

    /** @var float|int|null */
    private $from;

    /** @var float|int|null */
    private $to;

    public function __construct($from, $to, ?string $key = null)
    {
        $this->from = $from;
        $this->to = $to;
        $this->key = $key;
    }

    public function toArray(): array
    {
        $payload = [];

        if ($this->key !== null) {
            $payload['key'] = $this->key;
        }

        if ($this->to !== null) {
            $payload['to'] = $this->to;
        }

        if ($this->from !== null) {
            $payload['from'] = $this->from;
        }

        return $payload;
    }
}

class_alias(Range::class, 'Ibexa\Platform\ElasticSearchEngine\ElasticSearch\QueryDSL\Range');
