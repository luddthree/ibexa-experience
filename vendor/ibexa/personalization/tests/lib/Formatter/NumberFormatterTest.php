<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Formatter;

use Ibexa\Personalization\Formatter\NumberFormatter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

final class NumberFormatterTest extends TestCase
{
    private const LOCALE_EN = 'en';
    private const LOCALE_DE = 'de';

    /**
     * @dataProvider provideTestData
     */
    public function testShortenNumber(float $input, string $locale, string $expectedOutput, ?int $precision = null): void
    {
        $requestStack = $this->createMock(RequestStack::class);
        $translator = $this->createMock(TranslatorInterface::class);
        $translator
            ->method('trans')
            ->willReturnCallback(static function (string $abbr): string {
                $map = [
                    'number.format.thousand_abbreviation' => 'foo',
                    'number.format.million_abbreviation' => 'bar',
                ];

                return $map[$abbr] ?? 'unknown';
            });

        $request = new Request();
        $request->setLocale($locale);
        $numberFormatter = new NumberFormatter($requestStack, $translator);
        $requestStack
            ->method('getCurrentRequest')
            ->willReturn($request);

        $shortened = $precision === null
            ? $numberFormatter->shortenNumber($input)
            : $numberFormatter->shortenNumber($input, $precision);

        self::assertSame($expectedOutput, $shortened);
    }

    /**
     * @return iterable<array{int|float, string, string}>
     */
    public function provideTestData(): iterable
    {
        yield [
            800,
            self::LOCALE_EN,
            '800',
        ];

        yield [
            800,
            self::LOCALE_DE,
            '800',
        ];

        yield [
            800.0,
            self::LOCALE_EN,
            '800',
        ];

        yield [
            800.0,
            self::LOCALE_DE,
            '800',
        ];

        yield [
            9000,
            self::LOCALE_EN,
            '9foo',
        ];

        yield [
            9000,
            self::LOCALE_DE,
            '9foo',
        ];

        yield [
            9000,
            self::LOCALE_EN,
            '9foo',
            2,
        ];

        yield [
            9000,
            self::LOCALE_DE,
            '9foo',
            2,
        ];

        yield [
            9420,
            self::LOCALE_EN,
            '9.4foo',
        ];

        yield [
            9420,
            self::LOCALE_DE,
            '9,4foo',
        ];

        yield [
            9420,
            self::LOCALE_EN,
            '9.42foo',
            2,
        ];

        yield [
            9420,
            self::LOCALE_DE,
            '9,42foo',
            2,
        ];

        yield [
            9420,
            self::LOCALE_EN,
            '9.4200000000foo',
            10,
        ];

        yield [
            9420,
            self::LOCALE_DE,
            '9,4200000000foo',
            10,
        ];

        yield [
            420000000000,
            self::LOCALE_EN,
            '420,000bar',
        ];

        yield [
            420000000000,
            self::LOCALE_DE,
            '420.000bar',
        ];

        yield [
            420000000000.0,
            self::LOCALE_EN,
            '420,000bar',
        ];

        yield [
            420000000000.0,
            self::LOCALE_DE,
            '420.000bar',
        ];

        yield [
            -800,
            self::LOCALE_EN,
            '-800',
        ];

        yield [
            -800,
            self::LOCALE_DE,
            '-800',
        ];

        yield [
            -9420,
            self::LOCALE_EN,
            '-9.4foo',
        ];

        yield [
            -9420,
            self::LOCALE_DE,
            '-9,4foo',
        ];

        yield [
            -4200000000,
            self::LOCALE_EN,
            '-4,200,000foo',
        ];

        yield [
            -4200000000,
            self::LOCALE_DE,
            '-4.200.000foo',
        ];

        yield [
            -4200000000.0,
            self::LOCALE_EN,
            '-4,200,000foo',
        ];

        yield [
            -4200000000.0,
            self::LOCALE_DE,
            '-4.200.000foo',
        ];
    }
}

class_alias(NumberFormatterTest::class, 'Ibexa\Platform\Tests\Personalization\Formatter\NumberFormatterTest');
