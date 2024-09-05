<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\UI\Completeness\Entry;

use Ibexa\Bundle\ProductCatalog\UI\Completeness\Entry\PercentEntry;
use Ibexa\Contracts\ProductCatalog\Values\Common\Percent;
use PHPUnit\Framework\TestCase;

final class PercentEntryTest extends TestCase
{
    /**
     * @dataProvider provideForTestGetValidCompleteness
     */
    public function testGetValidCompleteness(float $value, int $expectedValue): void
    {
        $percentEntry = new PercentEntry(
            new Percent($value)
        );

        self::assertEquals(
            $percentEntry->getCompleteness()->getValue(),
            $expectedValue
        );
    }

    /**
     * @phpstan-return iterable<array{float, integer}>
     */
    public function provideForTestGetValidCompleteness(): iterable
    {
        yield [75, 75];
        yield [100, 100];
    }
}
