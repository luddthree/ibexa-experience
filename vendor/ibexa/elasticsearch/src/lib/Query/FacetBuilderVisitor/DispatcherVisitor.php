<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\FacetBuilderVisitor;

use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\FacetBuilder;
use Ibexa\Contracts\Elasticsearch\Query\FacetBuilderVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;

/**
 * @deprecated since eZ Platform 3.2.0, to be removed in Ibexa 4.0.0.
 */
final class DispatcherVisitor implements FacetBuilderVisitor
{
    /** @var \Ibexa\Contracts\Elasticsearch\Query\FacetBuilderVisitor[] */
    private $visitors;

    public function __construct(iterable $visitors)
    {
        $this->visitors = $visitors;
    }

    public function supports(FacetBuilder $builder, LanguageFilter $languageFilter): bool
    {
        return $this->findVisitor($builder, $languageFilter) !== null;
    }

    public function visit(FacetBuilderVisitor $dispatcher, FacetBuilder $builder, LanguageFilter $languageFilter): array
    {
        $visitor = $this->findVisitor($builder, $languageFilter);

        if ($visitor === null) {
            throw new NotImplementedException(
                'No visitor available for: ' . get_class($builder)
            );
        }

        return $visitor->visit($this, $builder, $languageFilter);
    }

    private function findVisitor(FacetBuilder $builder, LanguageFilter $languageFilter): ?FacetBuilderVisitor
    {
        foreach ($this->visitors as $visitor) {
            if ($visitor->supports($builder, $languageFilter)) {
                return $visitor;
            }
        }

        return null;
    }
}

class_alias(DispatcherVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\FacetBuilderVisitor\DispatcherVisitor');
