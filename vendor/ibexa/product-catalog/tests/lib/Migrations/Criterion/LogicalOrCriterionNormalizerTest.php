<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Migrations\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\LogicalOr;
use Ibexa\ProductCatalog\Migrations\Criterion\LogicalOrCriterionNormalizer;

/**
 * @covers \Ibexa\ProductCatalog\Migrations\Criterion\LogicalOrCriterionNormalizer
 *
 * @phpstan-extends \Ibexa\Tests\ProductCatalog\Migrations\Criterion\AbstractCompositeCriterionNormalizerTest<
 *     \Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\LogicalOr,
 * >
 */
final class LogicalOrCriterionNormalizerTest extends AbstractCompositeCriterionNormalizerTest
{
    protected function setUp(): void
    {
        $this->normalizer = new LogicalOrCriterionNormalizer();
    }

    protected function getHandledType(): string
    {
        return 'or';
    }

    protected function getHandledClass(): string
    {
        return LogicalOr::class;
    }
}
