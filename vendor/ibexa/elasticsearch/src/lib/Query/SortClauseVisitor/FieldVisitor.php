<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\SortClauseVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Contracts\Elasticsearch\Query\SortClauseVisitor;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Search\Common\FieldNameResolver;
use Ibexa\Elasticsearch\ElasticSearch\Index\FieldTypeResolver;

final class FieldVisitor extends AbstractSortClauseVisitor
{
    /** @var \Ibexa\Core\Search\Common\FieldNameResolver */
    private $fieldNameResolver;

    /** @var \Ibexa\Elasticsearch\ElasticSearch\Index\FieldTypeResolver */
    private $fieldTypeResolver;

    public function __construct(FieldNameResolver $fieldNameResolver, FieldTypeResolver $fieldTypeResolver)
    {
        $this->fieldNameResolver = $fieldNameResolver;
        $this->fieldTypeResolver = $fieldTypeResolver;
    }

    public function supports(SortClause $sortClause, LanguageFilter $languageFilter): bool
    {
        return $sortClause instanceof SortClause\Field;
    }

    public function visit(SortClauseVisitor $visitor, SortClause $sortClause, LanguageFilter $languageFilter): array
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\Target\FieldTarget $target */
        $target = $sortClause->targetData;

        $fieldName = $this->fieldNameResolver->getSortFieldName(
            $sortClause,
            $target->typeIdentifier,
            $target->fieldIdentifier
        );

        if ($fieldName === null) {
            throw new InvalidArgumentException(
                '$sortClause->targetData',
                'No searchable Fields found for the provided Sort Clause target ' .
                "'{$target->fieldIdentifier}' on '{$target->typeIdentifier}'."
            );
        }

        $fieldType = $this->fieldTypeResolver->getSortFieldType(
            $target->typeIdentifier,
            $target->fieldIdentifier
        );

        if ($fieldType === null) {
            throw new InvalidArgumentException(
                '$sortClause->targetData',
                'Unable to resolve field type for the provided Sort Clause target ' .
                "'{$target->fieldIdentifier}' on '{$target->typeIdentifier}'."
            );
        }

        return [
            $fieldName => [
                'order' => $this->getDirection($sortClause),
                'unmapped_type' => $fieldType,
            ],
        ];
    }
}

class_alias(FieldVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\SortClauseVisitor\FieldVisitor');
