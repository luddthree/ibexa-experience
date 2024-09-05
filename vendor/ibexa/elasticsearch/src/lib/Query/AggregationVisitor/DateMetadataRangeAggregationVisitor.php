<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\AggregationVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\AbstractRangeAggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\DateMetadataRangeAggregation;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;

final class DateMetadataRangeAggregationVisitor extends AbstractDateRangeAggregationVisitor
{
    /** @var string */
    private $supportedType;

    /** @var string */
    private $searchIndexFieldName;

    public function __construct(string $supportedType, string $searchIndexFieldName)
    {
        $this->supportedType = $supportedType;
        $this->searchIndexFieldName = $searchIndexFieldName;
    }

    public function supports(Aggregation $aggregation, LanguageFilter $languageFilter): bool
    {
        return $aggregation instanceof DateMetadataRangeAggregation && $aggregation->getType() === $this->supportedType;
    }

    protected function getTargetField(AbstractRangeAggregation $aggregation): string
    {
        return $this->searchIndexFieldName;
    }
}

class_alias(DateMetadataRangeAggregationVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\AggregationVisitor\DateMetadataRangeAggregationVisitor');
