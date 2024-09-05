<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\Query;

use Doctrine\Common\Collections\Expr\Expression;
use Ibexa\Contracts\ActivityLog\CriterionMapperInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\CriterionInterface;
use LogicException;

/**
 * @implements \Ibexa\Contracts\ActivityLog\CriterionMapperInterface<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\CriterionInterface>
 */
final class CriterionMapper implements CriterionMapperInterface
{
    /** @var iterable<\Ibexa\Contracts\ActivityLog\CriterionMapperInterface<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\CriterionInterface>> */
    private iterable $mappers;

    /**
     * @param iterable<\Ibexa\Contracts\ActivityLog\CriterionMapperInterface<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\CriterionInterface>> $mappers
     */
    public function __construct(iterable $mappers)
    {
        $this->mappers = $mappers;
    }

    public function handle(CriterionInterface $criterion): Expression
    {
        foreach ($this->mappers as $mapper) {
            if ($mapper->canHandle($criterion)) {
                return $mapper->handle($criterion);
            }
        }

        throw new LogicException(sprintf(
            'Unable to handle "%s" criterion. '
            . 'Ensure that a %s service exists for this criterion and is tagged with %s',
            get_class($criterion),
            CriterionMapperInterface::class,
            'ibexa.activity_log.query.criterion_mapper',
        ));
    }

    public function canHandle(CriterionInterface $criterion): bool
    {
        foreach ($this->mappers as $mapper) {
            if ($mapper->canHandle($criterion)) {
                return true;
            }
        }

        return false;
    }
}
