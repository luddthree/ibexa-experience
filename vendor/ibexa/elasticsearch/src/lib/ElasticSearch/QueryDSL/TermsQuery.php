<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\QueryDSL;

final class TermsQuery implements Query
{
    /** @var string|null */
    private $field;

    /** @var array|null */
    private $value;

    public function __construct(?string $field = null, ?array $value = null)
    {
        $this->field = $field;
        $this->value = $value;
    }

    public function withField(string $field): self
    {
        $this->field = $field;

        return $this;
    }

    public function withValue(array $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'terms' => [
                $this->field => $this->value,
            ],
        ];
    }
}

class_alias(TermsQuery::class, 'Ibexa\Platform\ElasticSearchEngine\ElasticSearch\QueryDSL\TermsQuery');
