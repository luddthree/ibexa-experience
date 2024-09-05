<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Elasticsearch\Query;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;

interface SortClauseVisitor
{
    public function supports(SortClause $sortClause, LanguageFilter $languageFilter): bool;

    public function visit(SortClauseVisitor $visitor, SortClause $sortClause, LanguageFilter $languageFilter): array;
}

class_alias(SortClauseVisitor::class, 'Ibexa\Platform\Contracts\ElasticSearchEngine\Query\SortClauseVisitor');
