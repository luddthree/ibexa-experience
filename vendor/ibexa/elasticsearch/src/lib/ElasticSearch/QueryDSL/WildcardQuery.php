<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\QueryDSL;

final class WildcardQuery implements Query
{
    /** @var string|null */
    private $field;

    /** @var string|null */
    private $value;

    public function __construct(?string $field = null, ?string $value = null)
    {
        $this->field = $field;
        $this->value = $value;
    }

    public function withField(string $field): self
    {
        $this->field = $field;

        return $this;
    }

    public function withValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return array{
     *     wildcard: array<
     *         string|null,
     *         array{
     *             value: string|null
     *         }
     *     >
     * }
     */
    public function toArray(): array
    {
        return [
            'wildcard' => [
                $this->field => [
                    'value' => $this->value,
                ],
            ],
        ];
    }
}

class_alias(WildcardQuery::class, 'Ibexa\Platform\ElasticSearchEngine\ElasticSearch\QueryDSL\WildcardQuery');
