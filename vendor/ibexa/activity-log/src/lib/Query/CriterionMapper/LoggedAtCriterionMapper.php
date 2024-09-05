<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\Query\CriterionMapper;

use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\Expression;
use Ibexa\Contracts\ActivityLog\CriterionMapperInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\CriterionInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\LoggedAtCriterion;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;

/**
 * @implements \Ibexa\Contracts\ActivityLog\CriterionMapperInterface<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\LoggedAtCriterion>
 */
final class LoggedAtCriterionMapper implements CriterionMapperInterface
{
    public function canHandle(CriterionInterface $criterion): bool
    {
        return $criterion instanceof LoggedAtCriterion;
    }

    public function handle(CriterionInterface $criterion): Expression
    {
        $op = $this->convertOperator($criterion->operator);

        return new Comparison('logged_at', $op, $criterion->dateTime->format('Y-m-d H:i:s'));
    }

    private function convertOperator(string $operator): string
    {
        switch ($operator) {
            case LoggedAtCriterion::GTE:
                return Comparison::GTE;
            case LoggedAtCriterion::GT:
                return Comparison::GT;
            case LoggedAtCriterion::EQ:
                return Comparison::EQ;
            case LoggedAtCriterion::LT:
                return Comparison::LT;
            case LoggedAtCriterion::LTE:
                return Comparison::LTE;
            case LoggedAtCriterion::NEQ:
                return Comparison::NEQ;
        }

        throw new InvalidArgumentException(
            '$criterion->operator',
            sprintf(
                'Unhandled comparison operator: %s. Known operators: "%s"',
                $operator,
                implode('", "', [
                    LoggedAtCriterion::GTE,
                    LoggedAtCriterion::GT,
                    LoggedAtCriterion::EQ,
                    LoggedAtCriterion::LT,
                    LoggedAtCriterion::LTE,
                    LoggedAtCriterion::NEQ,
                ]),
            ),
        );
    }
}
