<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\AggregationVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\AbstractTermAggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\UserMetadataTermAggregation;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;

final class UserMetadataTermAggregationVisitor extends AbstractTermAggregationVisitor
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
        return $aggregation instanceof UserMetadataTermAggregation && $aggregation->getType() === $this->supportedType;
    }

    protected function getTargetField(AbstractTermAggregation $aggregation): string
    {
        return $this->searchIndexFieldName;
    }
}

class_alias(UserMetadataTermAggregationVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\AggregationVisitor\UserMetadataTermAggregationVisitor');
