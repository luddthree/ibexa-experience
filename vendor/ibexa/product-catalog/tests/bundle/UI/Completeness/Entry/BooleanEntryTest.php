<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\UI\Completeness\Entry;

use Ibexa\Bundle\ProductCatalog\UI\Completeness\Entry\BooleanEntry;
use PHPUnit\Framework\TestCase;

final class BooleanEntryTest extends TestCase
{
    /**
     * @dataProvider provideForTestGetValidCompleteness
     */
    public function testGetValidCompleteness(bool $value, int $expectedValue): void
    {
        $booleanEntry = new BooleanEntry($value);

        self::assertEquals(
            $booleanEntry->getCompleteness()->getValue(),
            $expectedValue
        );
    }

    /**
     * @phpstan-return iterable<array{bool, integer}>
     */
    public function provideForTestGetValidCompleteness(): iterable
    {
        yield [true, 100];
        yield [false, 0];
    }

    /**
     * @dataProvider provideForTestGetInvalidCompleteness
     */
    public function testGetInvalidCompleteness(bool $value, int $expectedValue): void
    {
        $booleanEntry = new BooleanEntry($value);

        self::assertNotEquals(
            $booleanEntry->getCompleteness()->getValue(),
            $expectedValue
        );
    }

    /**
     * @phpstan-return iterable<array{mixed, mixed}>
     */
    public function provideForTestGetInvalidCompleteness(): iterable
    {
        yield [true, 50];
        yield [false, 200];
        yield [true, 0];
    }
}
