<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Aggregation;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\AbstractStatsAggregation;
use Ibexa\Contracts\Rest\Exceptions;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Server\Input\Parser\Aggregation\AbstractStatsAggregationParser;

abstract class AbstractAttributeStatsAggregationParser extends AbstractStatsAggregationParser
{
    /**
     * @param array<string, mixed> $data
     */
    final protected function parseAggregation(
        array $data,
        ParsingDispatcher $parsingDispatcher
    ): AbstractStatsAggregation {
        if (!array_key_exists('name', $data)) {
            throw new Exceptions\Parser(
                "Missing 'name' element for {$this->getAggregationName()}."
            );
        }

        if (!array_key_exists('attributeDefinitionIdentifier', $data)) {
            throw new Exceptions\Parser(
                "Missing 'attributeDefinitionIdentifier' element for {$this->getAggregationName()}."
            );
        }

        return $this->buildAttributeAggregation($data);
    }

    /**
     * @phpstan-param array{name: non-empty-string, attributeDefinitionIdentifier: non-empty-string} $data
     */
    abstract protected function buildAttributeAggregation(array $data): AbstractStatsAggregation;
}
