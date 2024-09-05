<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\LogicalOr;

/**
 * @phpstan-extends \Ibexa\ProductCatalog\Migrations\Criterion\AbstractCompositeCriterionNormalizer<
 *     \Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\LogicalOr,
 * >
 */
final class LogicalOrCriterionNormalizer extends AbstractCompositeCriterionNormalizer
{
    protected function getHandledClass(): string
    {
        return LogicalOr::class;
    }

    protected function getHandledType(): string
    {
        return 'or';
    }

    public function doDenormalize(array $data, string $type, string $format = null, array $context = []): CriterionInterface
    {
        $criteria = $this->convertCriteria($data, $format, $context);

        return new LogicalOr(...$criteria);
    }
}
