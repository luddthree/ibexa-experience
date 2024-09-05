<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\DateMetadata;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\Utils\DateFormatter;
use RuntimeException;

final class DateMetadataRangeVisitor extends AbstractRangeVisitor
{
    /** @var \Ibexa\Elasticsearch\ElasticSearch\QueryDSL\Utils\DateFormatter */
    private $dateFormatter;

    public function __construct(DateFormatter $dateFormatter)
    {
        $this->dateFormatter = $dateFormatter;
    }

    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        return $criterion instanceof DateMetadata && $this->isRangeOperator($criterion->operator);
    }

    protected function getTargetValue(Criterion $criterion): array
    {
        return [
            $this->dateFormatter->toElasticSearchDateTime($criterion->value[0] ?? null),
            $this->dateFormatter->toElasticSearchDateTime($criterion->value[1] ?? null),
        ];
    }

    protected function getTargetField(Criterion $criterion): string
    {
        switch ($criterion->target) {
            case Criterion\DateMetadata::CREATED:
            case Criterion\DateMetadata::PUBLISHED:
                return 'content_publication_date_dt';
            case Criterion\DateMetadata::MODIFIED:
                return 'content_modification_date_dt';
            default:
                throw new RuntimeException("Unsupported DateMetadata criterion target {$criterion->value}");
        }
    }
}

class_alias(DateMetadataRangeVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\DateMetadataRangeVisitor');
