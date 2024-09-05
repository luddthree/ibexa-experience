<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion;

use Ibexa\Contracts\Rest\Exceptions;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;

/**
 * @internal
 *
 * @template CR of object
 * @template SC of object
 */
final class CriterionProcessor
{
    private const CRITERION_ID_MAP = [
        'AND' => 'LogicalAnd',
        'OR' => 'LogicalOr',
        'NOT' => 'LogicalNot',
    ];

    /**
     * @param array<string, array<mixed>> $criteriaArray
     *
     * @phpstan-return CR[]
     */
    public function processCriteriaArray(array $criteriaArray, ParsingDispatcher $parsingDispatcher): array
    {
        if (empty($criteriaArray)) {
            return [];
        }

        $criteria = [];
        foreach ($criteriaArray as $criterionName => $criterionData) {
            $criteria[] = $this->dispatchCriterion($criterionName, $criterionData, $parsingDispatcher);
        }

        return $criteria;
    }

    /**
     * @param array<string, array<mixed>> $sortClausesArray
     *
     * @phpstan-return SC[]
     */
    public function processSortClauses(array $sortClausesArray, ParsingDispatcher $parsingDispatcher): array
    {
        $sortClauses = [];
        foreach ($sortClausesArray as $sortClauseName => $sortClauseData) {
            if (!is_array($sortClauseData) || !isset($sortClauseData[0])) {
                $sortClauseData = [$sortClauseData];
            }

            foreach ($sortClauseData as $data) {
                $sortClauses[] = $this->dispatchSortClause($sortClauseName, $data, $parsingDispatcher);
            }
        }

        return $sortClauses;
    }

    /**
     * @param mixed $criterionData
     *
     * @phpstan-return CR
     */
    private function dispatchCriterion(
        string $criterionName,
        $criterionData,
        ParsingDispatcher $parsingDispatcher
    ): object {
        $mediaType = $this->getCriterionMediaType($criterionName);

        try {
            return $parsingDispatcher->parse([$criterionName => $criterionData], $mediaType);
        } catch (Exceptions\Parser $e) {
            throw new Exceptions\Parser("Invalid Criterion id <$criterionName> in <AND>", 0, $e);
        }
    }

    private function getCriterionMediaType(string $criterionName): string
    {
        $criterionName = substr($criterionName, 0, -strlen('Criterion'));
        if (isset(self::CRITERION_ID_MAP[$criterionName])) {
            $criterionName = self::CRITERION_ID_MAP[$criterionName];
        }

        return 'application/vnd.ibexa.api.internal.criterion.' . $criterionName;
    }

    /**
     * @phpstan-return SC
     */
    private function dispatchSortClause(
        string $sortClauseName,
        string $direction,
        ParsingDispatcher $parsingDispatcher
    ): object {
        return $parsingDispatcher->parse(
            [$sortClauseName => $direction],
            'application/vnd.ibexa.api.internal.sortclause.' . $sortClauseName
        );
    }
}
