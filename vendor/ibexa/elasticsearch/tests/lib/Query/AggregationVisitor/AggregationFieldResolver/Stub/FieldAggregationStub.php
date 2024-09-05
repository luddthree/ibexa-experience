<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\AggregationVisitor\AggregationFieldResolver\Stub;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\FieldAggregation;

final class FieldAggregationStub implements Aggregation, FieldAggregation
{
    /** @var string */
    private $contentTypeIdentifier;

    /** @var string */
    private $fieldDefinitionIdentifier;

    public function __construct(string $contentTypeIdentifier, string $fieldDefinitionIdentifier)
    {
        $this->contentTypeIdentifier = $contentTypeIdentifier;
        $this->fieldDefinitionIdentifier = $fieldDefinitionIdentifier;
    }

    public function getName(): string
    {
        return 'test_aggregation';
    }

    public function getContentTypeIdentifier(): string
    {
        return $this->contentTypeIdentifier;
    }

    public function getFieldDefinitionIdentifier(): string
    {
        return $this->fieldDefinitionIdentifier;
    }
}

class_alias(FieldAggregationStub::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\AggregationVisitor\AggregationFieldResolver\Stub\FieldAggregationStub');
