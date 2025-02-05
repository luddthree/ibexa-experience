<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\SectionService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Section;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper;

final class SectionAggregationKeyMapper implements TermAggregationKeyMapper
{
    /** @var \Ibexa\Contracts\Core\Repository\SectionService */
    private $sectionService;

    public function __construct(SectionService $sectionService)
    {
        $this->sectionService = $sectionService;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\SectionTermAggregation $aggregation
     * @param string[] $keys
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Section[]
     */
    public function map(Aggregation $aggregation, LanguageFilter $languageFilter, array $keys): array
    {
        $result = [];
        foreach ($keys as $key) {
            try {
                $result[$key] = $this->sectionService->loadSection((int)$key);
            } catch (NotFoundException | UnauthorizedException $e) {
                // Skip missing section
            }
        }

        return $result;
    }
}

class_alias(SectionAggregationKeyMapper::class, 'Ibexa\Platform\ElasticSearchEngine\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper\SectionAggregationKeyMapper');
