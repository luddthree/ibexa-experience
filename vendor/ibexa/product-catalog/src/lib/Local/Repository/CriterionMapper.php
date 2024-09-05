<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository;

use Doctrine\Common\Collections\Expr\Expression;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface;
use LogicException;

/**
 * Converts Criterion instances into objects that underlying Handler can understand.
 */
final class CriterionMapper
{
    /**
     * @var iterable<\Ibexa\Contracts\ProductCatalog\Values\Common\Query\CriterionMapperInterface<
     *     \Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface,
     * >>
     */
    private iterable $mappers;

    /**
     * @phpstan-param iterable<\Ibexa\Contracts\ProductCatalog\Values\Common\Query\CriterionMapperInterface<
     *     \Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface,
     * >> $mappers
     */
    public function __construct(iterable $mappers)
    {
        $this->mappers = $mappers;
    }

    public function handle(CriterionInterface $criterion): Expression
    {
        foreach ($this->mappers as $mapper) {
            if ($mapper->canHandle($criterion)) {
                return $mapper->handle($criterion, $this);
            }
        }

        throw new LogicException(sprintf(
            'Unable to handle "%s" criterion.',
            get_class($criterion),
        ));
    }
}
