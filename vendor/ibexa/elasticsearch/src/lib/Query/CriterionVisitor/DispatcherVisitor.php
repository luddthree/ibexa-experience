<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Elasticsearch\Query\CriterionVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;

final class DispatcherVisitor implements CriterionVisitor
{
    /** @var \Ibexa\Contracts\Elasticsearch\Query\CriterionVisitor[] */
    private $visitors;

    public function __construct(iterable $visitors = [])
    {
        $this->visitors = $visitors;
    }

    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        return $this->findVisitor($criterion, $languageFilter) !== null;
    }

    public function visit(CriterionVisitor $dispatcher, Criterion $criterion, LanguageFilter $languageFilter): array
    {
        $visitor = $this->findVisitor($criterion, $languageFilter);
        if ($visitor === null) {
            throw new NotImplementedException(
                'No visitor available for: ' . get_class($criterion) . ' with operator ' . $criterion->operator
            );
        }

        return $visitor->visit($this, $criterion, $languageFilter);
    }

    private function findVisitor(Criterion $criterion, LanguageFilter $languageFilter): ?CriterionVisitor
    {
        foreach ($this->visitors as $visitor) {
            if ($visitor->supports($criterion, $languageFilter)) {
                return $visitor;
            }
        }

        return null;
    }
}

class_alias(DispatcherVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\DispatcherVisitor');
