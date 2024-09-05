<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\SortClauseVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Contracts\Elasticsearch\Query\SortClauseVisitor;

final class ContentTranslatedNameVisitor extends AbstractSortClauseVisitor
{
    public function supports(SortClause $sortClause, LanguageFilter $languageFilter): bool
    {
        return $sortClause instanceof SortClause\ContentTranslatedName;
    }

    public function visit(
        SortClauseVisitor $visitor,
        SortClause $sortClause,
        LanguageFilter $languageFilter
    ): array {
        return [
            'content_translated_name_s' => $this->getDirection($sortClause),
        ];
    }
}

class_alias(ContentTranslatedNameVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\SortClauseVisitor\ContentTranslatedNameVisitor');
