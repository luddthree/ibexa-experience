<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\NumberFormatter;

use Ibexa\ProductCatalog\NumberFormatter\LocaleProvider\LocaleResolverInterface;
use Ibexa\ProductCatalog\NumberFormatter\NumberFormatterFactory;
use NumberFormatter;
use PHPUnit\Framework\TestCase;

final class NumberFormatterFactoryTest extends TestCase
{
    private const EXAMPLE_LOCALE = 'en';
    private const EXAMPLE_STYLE = NumberFormatter::DECIMAL;
    private const EXAMPLE_PATTERN = '#,##0.00########';
    private const EXAMPLE_ATTRIBUTES = [
        NumberFormatter::MAX_FRACTION_DIGITS => 10,
        NumberFormatter::MIN_FRACTION_DIGITS => 2,
    ];

    public function testCreateNumberFormatter(): void
    {
        $localeResolver = $this->createMock(LocaleResolverInterface::class);
        $localeResolver->method('getCurrentLocale')->willReturn(self::EXAMPLE_LOCALE);

        $factory = new NumberFormatterFactory(
            $localeResolver,
            self::EXAMPLE_STYLE,
            self::EXAMPLE_PATTERN,
            self::EXAMPLE_ATTRIBUTES
        );

        $this->assertFormatter($factory->createNumberFormatter());
    }

    public function testCreateNumberFormatterWithForcedLocale(): void
    {
        $localeResolver = $this->createMock(LocaleResolverInterface::class);
        $localeResolver->expects(self::never())->method('getCurrentLocale');

        $factory = new NumberFormatterFactory(
            $localeResolver,
            self::EXAMPLE_STYLE,
            self::EXAMPLE_PATTERN,
            self::EXAMPLE_ATTRIBUTES
        );

        $this->assertFormatter($factory->createNumberFormatter(self::EXAMPLE_LOCALE));
    }

    private function assertFormatter(NumberFormatter $formatter): void
    {
        self::assertEquals(self::EXAMPLE_LOCALE, $formatter->getLocale());
        self::assertEquals(self::EXAMPLE_PATTERN, $formatter->getPattern());
        foreach (self::EXAMPLE_ATTRIBUTES as $attribute => $value) {
            self::assertEquals($value, $formatter->getAttribute($attribute));
        }
    }
}
