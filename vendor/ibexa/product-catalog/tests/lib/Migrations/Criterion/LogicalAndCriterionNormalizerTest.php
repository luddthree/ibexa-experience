<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Migrations\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\LogicalAnd;
use Ibexa\ProductCatalog\Migrations\Criterion\LogicalAndCriterionNormalizer;

/**
 * @covers \Ibexa\ProductCatalog\Migrations\Criterion\LogicalAndCriterionNormalizer
 *
 * @phpstan-extends \Ibexa\Tests\ProductCatalog\Migrations\Criterion\AbstractCompositeCriterionNormalizerTest<
 *     \Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\LogicalAnd,
 * >
 */
final class LogicalAndCriterionNormalizerTest extends AbstractCompositeCriterionNormalizerTest
{
    protected function setUp(): void
    {
        $this->normalizer = new LogicalAndCriterionNormalizer();
    }

    protected function getHandledType(): string
    {
        return 'and';
    }

    protected function getHandledClass(): string
    {
        return LogicalAnd::class;
    }
}
