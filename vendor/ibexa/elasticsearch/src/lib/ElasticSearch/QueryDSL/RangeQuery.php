<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\QueryDSL;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use RuntimeException;

final class RangeQuery implements Query
{
    /** @var string|null */
    private $field;

    /** @var string|null */
    private $operator;

    /** @var mixed */
    private $a;

    /** @var mixed */
    private $b;

    public function __construct(?string $field = null, ?string $operator = null, array $value = [])
    {
        $this->field = $field;
        $this->operator = $operator;
        if (!empty($value)) {
            list($this->a, $this->b) = array_pad($value, 2, null);
        }
    }

    public function withField(string $field): self
    {
        $this->field = $field;

        return $this;
    }

    public function withOperator(string $operator): self
    {
        $this->operator = $operator;

        return $this;
    }

    public function withRange($a, $b = null): self
    {
        $this->a = $a;
        $this->b = $b;

        return $this;
    }

    public function toArray(): array
    {
        if ($this->operator === Operator::BETWEEN) {
            return [
                'range' => [
                    $this->field => [
                        'gte' => $this->a,
                        'lte' => $this->b,
                    ],
                ],
            ];
        }

        return [
            'range' => [
                $this->field => [
                    $this->toElasticSearchOperator($this->operator) => $this->a,
                ],
            ],
        ];
    }

    private function toElasticSearchOperator(string $operator): string
    {
        switch ($operator) {
            case Operator::GT:
                return 'gt';
            case Operator::GTE:
                return 'gte';
            case Operator::LT:
                return 'lt';
            case Operator::LTE:
                return 'lte';
            default:
                throw new RuntimeException('Unsupported operator: ' . $operator);
        }
    }
}

class_alias(RangeQuery::class, 'Ibexa\Platform\ElasticSearchEngine\ElasticSearch\QueryDSL\RangeQuery');
