<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\REST\Input\Parser;

use Ibexa\ActivityLog\REST\Input\Parser\Criterion\Criteria;
use Ibexa\ActivityLog\REST\Input\Parser\SortClause\SortClauses;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Query;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\SortClause\LoggedAtSortClause;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;

final class ActivityLogGroupListInput extends AbstractParser
{
    private const OFFSET_KEY = 'offset';
    private const LIMIT_KEY = 'limit';
    private const CRITERIA_KEY = 'criteria';
    private const SORT_CLAUSES_KEY = 'sortClauses';
    private const OBJECT_KEYS = [
        self::OFFSET_KEY,
        self::LIMIT_KEY,
        self::CRITERIA_KEY,
        self::SORT_CLAUSES_KEY,
    ];

    private ConfigResolverInterface $configResolver;

    public function __construct(
        ConfigResolverInterface $configResolver
    ) {
        $this->configResolver = $configResolver;
    }

    protected function getOptionalKeys(): ?array
    {
        return self::OBJECT_KEYS;
    }

    protected function getName(): string
    {
        return 'ActivityLogGroupList';
    }

    /**
     * @phpstan-param array<mixed> $data
     */
    public function innerParse(array $data, ParsingDispatcher $parsingDispatcher): Query
    {
        $limit = $this->getLimit($data);
        $offset = $this->getOffset($data);

        $criteria = $this->getCriteria($data, $parsingDispatcher);
        $sortClauses = $this->getSortClauses($data, $parsingDispatcher);

        return new Query($criteria, $sortClauses, $offset, $limit);
    }

    /**
     * @param array<mixed> $data
     */
    private function getOffset(array $data): int
    {
        if (isset($data[self::OFFSET_KEY])) {
            if (!is_numeric($data[self::OFFSET_KEY])) {
                throw new Parser('Offset property is not numeric.');
            }

            return (int)$data[self::OFFSET_KEY];
        }

        return 0;
    }

    /**
     * @param array<mixed> $data
     */
    private function getLimit(array $data): int
    {
        if (isset($data[self::LIMIT_KEY])) {
            // TODO: Validate using Symfony Validator
            if (!is_numeric($data[self::LIMIT_KEY])) {
                throw new Parser('Limit property is not numeric.');
            }

            return (int)$data[self::LIMIT_KEY];
        }

        return $this->configResolver->getParameter('activity_log.pagination.activity_logs_limit');
    }

    /**
     * @param array<mixed> $data
     *
     * @return array<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\CriterionInterface>
     */
    private function getCriteria(array $data, ParsingDispatcher $parsingDispatcher): array
    {
        if (!isset($data[self::CRITERIA_KEY])) {
            return [];
        }

        $criteria = $data[self::CRITERIA_KEY];
        if (!is_array($criteria)) {
            throw new Parser(sprintf('The "%s" parameter must be an array.', self::CRITERIA_KEY));
        }

        return $parsingDispatcher->parse($criteria, Criteria::MEDIA_TYPE);
    }

    /**
     * @param array<mixed> $data
     *
     * @return array<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\SortClause\SortClauseInterface>
     */
    private function getSortClauses(array $data, ParsingDispatcher $parsingDispatcher): array
    {
        if (!isset($data[self::SORT_CLAUSES_KEY])) {
            return [
                new LoggedAtSortClause('ASC'),
            ];
        }

        $sortClauses = $data[self::SORT_CLAUSES_KEY];

        if (!is_array($sortClauses)) {
            throw new Parser(sprintf('The "%s" parameter must be an array.', self::SORT_CLAUSES_KEY));
        }

        return $parsingDispatcher->parse($sortClauses, SortClauses::MEDIA_TYPE);
    }
}
