<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\Query\CriterionMapper;

use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\Common\Collections\Expr\Expression;
use Ibexa\Contracts\ActivityLog\CriterionMapperInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\CompositeCriterion;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\CriterionInterface;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;

/**
 * @implements \Ibexa\Contracts\ActivityLog\CriterionMapperInterface<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\CompositeCriterion>
 */
final class CompositeCriterionMapper implements CriterionMapperInterface
{
    /**
     * @phpstan-var \Ibexa\Contracts\ActivityLog\CriterionMapperInterface<
     *     \Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\CriterionInterface,
     * >
     */
    private CriterionMapperInterface $criterionMapper;

    /**
     * @phpstan-param \Ibexa\Contracts\ActivityLog\CriterionMapperInterface<
     *     \Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\CriterionInterface,
     * > $criterionMapper
     */
    public function __construct(CriterionMapperInterface $criterionMapper)
    {
        $this->criterionMapper = $criterionMapper;
    }

    public function canHandle(CriterionInterface $criterion): bool
    {
        return $criterion instanceof CompositeCriterion;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    public function handle(CriterionInterface $criterion): CompositeExpression
    {
        $type = $this->convertType($criterion->getType());

        return new CompositeExpression($type, array_map(
            fn (CriterionInterface $criterion): Expression => $this->criterionMapper->handle($criterion),
            $criterion->criteria,
        ));
    }

    /**
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    private function convertType(string $type): string
    {
        switch ($type) {
            case CompositeCriterion::TYPE_AND:
                return CompositeExpression::TYPE_AND;
            case CompositeCriterion::TYPE_OR:
                return CompositeExpression::TYPE_OR;
            default:
                throw new InvalidArgumentException('$type', sprintf(
                    'Unexpected type received, cannot perform conversion. Expected one of: "%s". Received "%s".',
                    implode('", "', [CompositeCriterion::TYPE_OR, CompositeCriterion::TYPE_AND]),
                    $type,
                ));
        }
    }
}
