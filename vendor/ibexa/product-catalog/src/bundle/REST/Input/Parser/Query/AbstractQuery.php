<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query;

use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion\CriterionProcessor;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;

/**
 * @template CR of object
 * @template SC of object
 */
abstract class AbstractQuery extends BaseParser
{
    protected const FILTER = 'Filter';
    protected const QUERY = 'Query';
    protected const SORT_CLAUSES = 'SortClauses';
    protected const AGGREGATIONS = 'Aggregations';

    /** @var CriterionProcessor<CR, SC> */
    private CriterionProcessor $criterionProcessor;

    /** @param CriterionProcessor<CR, SC> $criterionProcessor */
    public function __construct(CriterionProcessor $criterionProcessor)
    {
        $this->criterionProcessor = $criterionProcessor;
    }

    /**
     * @param array<mixed> $data
     */
    abstract protected function buildQuery(array $data, ParsingDispatcher $parsingDispatcher): object;

    /**
     * @return string[]
     */
    abstract protected function getAllowedKeys(): array;

    /**
     * @param array<mixed> $data
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): object
    {
        if (!empty($redundantKeys = $this->checkRedundantKeys(array_keys($data)))) {
            throw new Parser(
                sprintf(
                    'The following properties are redundant: %s.',
                    implode(', ', $redundantKeys)
                )
            );
        }

        $query = $this->buildQuery($data, $parsingDispatcher);

        if (array_key_exists('limit', $data)) {
            $query->setLimit((int)$data['limit']);
        }

        if (array_key_exists('offset', $data)) {
            $query->setOffset((int)$data['offset']);
        }

        return $query;
    }

    /**
     * @param array<string, array<mixed>> $criteriaArray
     *
     * @phpstan-return CR[]
     */
    protected function processCriteriaArray(array $criteriaArray, ParsingDispatcher $parsingDispatcher): array
    {
        return $this->criterionProcessor->processCriteriaArray($criteriaArray, $parsingDispatcher);
    }

    /**
     * @param array<string, array<mixed>> $sortClausesArray
     *
     * @phpstan-return SC[]
     */
    protected function processSortClauses(array $sortClausesArray, ParsingDispatcher $parsingDispatcher): array
    {
        return $this->criterionProcessor->processSortClauses($sortClausesArray, $parsingDispatcher);
    }

    /**
     * @param string[] $providedKeys
     *
     * @return string[]
     */
    private function checkRedundantKeys(array $providedKeys): array
    {
        $allowedKeys = array_merge(
            $this->getAllowedKeys(),
            ['limit', 'offset']
        );

        return array_diff($providedKeys, $allowedKeys);
    }
}
