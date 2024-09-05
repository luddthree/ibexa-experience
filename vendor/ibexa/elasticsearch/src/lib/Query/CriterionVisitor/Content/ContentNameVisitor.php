<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor\Content;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Elasticsearch\Query\CriterionVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\WildcardQuery;

/**
 * @internal
 */
final class ContentNameVisitor implements CriterionVisitor
{
    private const FIELD_NAME = 'content_translated_name_s';

    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        return $criterion instanceof Criterion\ContentName
            && $criterion->operator === Criterion\Operator::LIKE;
    }

    /**
     * @return array{
     *      wildcard: array<
     *          string,
     *          array{
     *              value: string|null
     *          }
     *      >
     *  }
     */
    public function visit(
        CriterionVisitor $dispatcher,
        Criterion $criterion,
        LanguageFilter $languageFilter
    ): array {
        /** @var string $value */
        $value = $criterion->value;

        return (new WildcardQuery(self::FIELD_NAME, $value))->toArray();
    }
}
