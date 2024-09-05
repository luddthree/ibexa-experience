<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\SortClauseVisitor;

use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Contracts\Elasticsearch\Query\SortClauseVisitor;

final class DispatcherVisitor implements SortClauseVisitor
{
    /** @var \Ibexa\Contracts\Elasticsearch\Query\SortClauseVisitor[] */
    private $visitors;

    public function __construct(iterable $visitors)
    {
        $this->visitors = $visitors;
    }

    public function supports(SortClause $sortClause, LanguageFilter $languageFilter): bool
    {
        return $this->findVisitor($sortClause, $languageFilter) !== null;
    }

    public function visit(SortClauseVisitor $visitor, SortClause $sortClause, LanguageFilter $languageFilter): array
    {
        $visitor = $this->findVisitor($sortClause, $languageFilter);

        if ($visitor === null) {
            throw new NotImplementedException(
                'No visitor available for: ' . get_class($sortClause)
            );
        }

        return $visitor->visit($this, $sortClause, $languageFilter);
    }

    private function findVisitor(SortClause $sortClause, LanguageFilter $languageFilter): ?SortClauseVisitor
    {
        foreach ($this->visitors as $visitor) {
            if ($visitor->supports($sortClause, $languageFilter)) {
                return $visitor;
            }
        }

        return null;
    }
}

class_alias(DispatcherVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\SortClauseVisitor\DispatcherVisitor');
