<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Elasticsearch\Query\CriterionVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\BoolQuery;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\TermQuery;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\Utils\DateFormatter;
use RuntimeException;

final class DateMetadataInVisitor implements CriterionVisitor
{
    /** @var \Ibexa\Elasticsearch\ElasticSearch\QueryDSL\Utils\DateFormatter */
    private $dateFormatter;

    public function __construct(DateFormatter $dateFormatter)
    {
        $this->dateFormatter = $dateFormatter;
    }

    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        if ($criterion instanceof Criterion\DateMetadata) {
            return $criterion->operator === Operator::EQ || $criterion->operator === Operator::IN;
        }

        return false;
    }

    public function visit(CriterionVisitor $dispatcher, Criterion $criterion, LanguageFilter $languageFilter): array
    {
        $fieldName = $this->getTargetField($criterion);

        $query = new BoolQuery();
        foreach ($criterion->value as $value) {
            $query->addShould(new TermQuery(
                $fieldName,
                $this->dateFormatter->toElasticSearchDateTime($value)
            ));
        }

        return $query->toArray();
    }

    private function getTargetField(Criterion $criterion): string
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

class_alias(DateMetadataInVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\DateMetadataInVisitor');
