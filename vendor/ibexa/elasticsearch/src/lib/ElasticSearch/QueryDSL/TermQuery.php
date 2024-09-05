<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\QueryDSL;

final class TermQuery implements Query
{
    /** @var string|null */
    private $field;

    /** @var mixed */
    private $value;

    public function __construct(?string $field = null, $value = null)
    {
        $this->field = $field;
        $this->value = $value;
    }

    public function withField(string $field): self
    {
        $this->field = $field;

        return $this;
    }

    public function withValue($value): self
    {
        $this->value = $value;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'term' => [
                $this->field => $this->value,
            ],
        ];
    }
}

class_alias(TermQuery::class, 'Ibexa\Platform\ElasticSearchEngine\ElasticSearch\QueryDSL\TermQuery');
