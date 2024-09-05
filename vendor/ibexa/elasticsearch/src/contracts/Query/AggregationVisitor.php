<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Elasticsearch\Query;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;

interface AggregationVisitor
{
    /**
     * Returns true if visitor supports given aggregation.
     */
    public function supports(Aggregation $aggregation, LanguageFilter $languageFilter): bool;

    /**
     * Builds aggregation query.
     */
    public function visit(
        AggregationVisitor $dispatcher,
        Aggregation $aggregation,
        LanguageFilter $languageFilter
    ): array;
}

class_alias(AggregationVisitor::class, 'Ibexa\Platform\Contracts\ElasticSearchEngine\Query\AggregationVisitor');
