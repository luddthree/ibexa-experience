<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor\Iterator;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Search\Field as SearchField;
use Ibexa\Contracts\Core\Search\FieldType;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Search\Common\FieldNameResolver;
use Ibexa\Core\Search\Common\FieldValueMapper;
use IteratorAggregate;
use Traversable;

final class FieldCriterionTargetIterator implements IteratorAggregate
{
    /** @var \Ibexa\Core\Search\Common\FieldNameResolver */
    private $fieldNameResolver;

    /** @var \Ibexa\Core\Search\Common\FieldValueMapper */
    private $fieldValueMapper;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion */
    private $criterion;

    public function __construct(
        FieldNameResolver $fieldNameResolver,
        FieldValueMapper $fieldValueMapper,
        Criterion $criterion
    ) {
        $this->fieldNameResolver = $fieldNameResolver;
        $this->fieldValueMapper = $fieldValueMapper;
        $this->criterion = $criterion;
    }

    public function getIterator(): Traversable
    {
        $fields = $this->fieldNameResolver->getFieldTypes(
            $this->criterion,
            $this->criterion->target
        );

        if (empty($fields)) {
            throw new InvalidArgumentException(
                '$criterion->target',
                "No searchable Fields found for the provided Criterion target '{$this->criterion->target}'."
            );
        }

        $values = (array)$this->criterion->value;

        if (empty($values)) {
            yield from [];
        }

        foreach ($fields as $fieldName => $fieldType) {
            if ($this->criterion->operator === Criterion\Operator::BETWEEN) {
                yield $fieldName => array_map(function ($value) use ($fieldType) {
                    return $this->prepareValue($value, $fieldType);
                }, $values);
            } else {
                foreach ($values as $value) {
                    yield $fieldName => $this->prepareValue($value, $fieldType);
                }
            }
        }
    }

    /**
     * Map search field value to solr value using FieldValueMapper.
     */
    private function prepareValue($value, FieldType $searchFieldType = null)
    {
        if (null === $searchFieldType) {
            return (string)$value;
        }

        $value = (array)$this->fieldValueMapper->map(
            new SearchField('field', $value, $searchFieldType)
        );

        return current($value);
    }
}

class_alias(FieldCriterionTargetIterator::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\Iterator\FieldCriterionTargetIterator');
