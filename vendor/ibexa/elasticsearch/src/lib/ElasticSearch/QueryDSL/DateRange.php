<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\QueryDSL;

use DateTimeInterface;

final class DateRange
{
    /** @var string|null */
    private $key;

    /** @var \DateTimeInterface|null */
    private $from;

    /** @var \DateTimeInterface|null */
    private $to;

    public function __construct(?DateTimeInterface $from, ?DateTimeInterface $to, ?string $key = null)
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
            $payload['to'] = $this->formatRangeBorder($this->to);
        }

        if ($this->from !== null) {
            $payload['from'] = $this->formatRangeBorder($this->from);
        }

        return $payload;
    }

    private function formatRangeBorder(?DateTimeInterface $value): ?string
    {
        if ($value instanceof DateTimeInterface) {
            $value = $value->format(DateTimeInterface::ISO8601);
        }

        return $value;
    }
}

class_alias(DateRange::class, 'Ibexa\Platform\ElasticSearchEngine\ElasticSearch\QueryDSL\DateRange');
