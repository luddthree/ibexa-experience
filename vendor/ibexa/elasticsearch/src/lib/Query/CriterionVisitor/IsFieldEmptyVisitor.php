<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\IsFieldEmpty;
use Ibexa\Contracts\Core\Search\FieldType\BooleanField;
use Ibexa\Contracts\Elasticsearch\Query\CriterionVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Search\Common\FieldNameGenerator;
use Ibexa\Core\Search\Common\FieldNameResolver;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\BoolQuery;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\TermQuery;

final class IsFieldEmptyVisitor implements CriterionVisitor
{
    /** @var \Ibexa\Core\Search\Common\FieldNameResolver */
    private $fieldNameResolver;

    /** @var \Ibexa\Core\Search\Common\FieldNameGenerator */
    private $fieldNameGenerator;

    public function __construct(FieldNameResolver $fieldNameResolver, FieldNameGenerator $fieldNameGenerator)
    {
        $this->fieldNameResolver = $fieldNameResolver;
        $this->fieldNameGenerator = $fieldNameGenerator;
    }

    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        return $criterion instanceof IsFieldEmpty;
    }

    public function visit(CriterionVisitor $dispatcher, Criterion $criterion, LanguageFilter $languageFilter): array
    {
        $searchFields = $this->fieldNameResolver->getFieldTypes($criterion, $criterion->target);

        if (empty($searchFields)) {
            throw new InvalidArgumentException(
                '$criterion->target',
                "No searchable fields found for the given criterion target '{$criterion->target}'."
            );
        }

        $values = (array)$criterion->value;

        $qb = new BoolQuery();
        foreach ($searchFields as $fieldName => $fieldType) {
            foreach ($values as $value) {
                $name = $this->fieldNameGenerator->getTypedName(
                    $this->fieldNameGenerator->getName(
                        'is_empty',
                        $criterion->target
                    ),
                    new BooleanField()
                );

                $qb->addShould(new TermQuery($name, (bool)$value));
            }
        }

        return $qb->toArray();
    }
}

class_alias(IsFieldEmptyVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\IsFieldEmptyVisitor');
