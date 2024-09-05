<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\QueryDSL;

/**
 * A multi-bucket aggregation where buckets are dynamically built - one per unique value.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-terms-aggregation.html
 */
final class TermsAggregation implements Aggregation
{
    /** @var string */
    private $fieldName;

    /** @var int|null */
    private $size;

    /** @var int|null */
    private $minDocCount;

    /** @var string|string[]|null */
    private $include;

    /** @var string|string[]|null */
    private $exclude;

    public function __construct(string $fieldName, ?int $size = null, ?int $minDocCount = null)
    {
        $this->fieldName = $fieldName;
        $this->size = $size;
        $this->minDocCount = $minDocCount;
        $this->include = null;
        $this->exclude = null;
    }

    public function withSize(?int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function withMinDocCount(?int $minDocCount): self
    {
        $this->minDocCount = $minDocCount;

        return $this;
    }

    public function withIncludeRegex(string $regex): self
    {
        $this->include = $regex;

        return $this;
    }

    public function withIncludeValues(array $values): self
    {
        $this->include = $values;

        return $this;
    }

    public function withExcludeRegex(string $regex): self
    {
        $this->exclude = $regex;

        return $this;
    }

    public function withExcludeValues(array $values): self
    {
        $this->exclude = $values;

        return $this;
    }

    public function toArray(): array
    {
        $payload = [
            'field' => $this->fieldName,
        ];

        if ($this->size !== null) {
            $payload['size'] = $this->size;
        }

        if ($this->minDocCount !== null) {
            $payload['min_doc_count'] = $this->minDocCount;
        }

        if ($this->include !== null) {
            $payload['include'] = $this->include;
        }

        if ($this->exclude !== null) {
            $payload['exclude'] = $this->exclude;
        }

        return [
            'terms' => $payload,
        ];
    }
}

class_alias(TermsAggregation::class, 'Ibexa\Platform\ElasticSearchEngine\ElasticSearch\QueryDSL\TermsAggregation');
