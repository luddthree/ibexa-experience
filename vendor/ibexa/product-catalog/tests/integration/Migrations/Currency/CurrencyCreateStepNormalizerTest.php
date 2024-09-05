<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Migrations\Currency;

use Ibexa\ProductCatalog\Migrations\Currency\CurrencyCreateStep;
use Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractNormalizerTest;

/**
 * @covers \Ibexa\ProductCatalog\Migrations\Currency\CurrencyCreateStepNormalizer
 *
 * @extends \Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractNormalizerTest<\Ibexa\ProductCatalog\Migrations\Currency\CurrencyCreateStep>
 */
final class CurrencyCreateStepNormalizerTest extends AbstractNormalizerTest
{
    protected static function getHandledClass(): string
    {
        return CurrencyCreateStep::class;
    }

    public function provideForSerialization(): iterable
    {
        yield [
            new CurrencyCreateStep('foo', 2, true),
            <<<YAML
            type: currency
            mode: create
            code: foo
            subunits: 2
            enabled: true

            YAML,
        ];

        yield [
            new CurrencyCreateStep('bar', 4, false),
            <<<YAML
            type: currency
            mode: create
            code: bar
            subunits: 4
            enabled: false

            YAML,
        ];
    }

    public function provideForDeserialization(): iterable
    {
        yield [
            <<<YAML
            type: currency
            mode: create
            code: foo
            subunits: 2
            YAML,
            static function (object $step): void {
                self::assertInstanceOf(CurrencyCreateStep::class, $step);
                self::assertSame('foo', $step->getCode());
                self::assertSame(2, $step->getSubunits());
                self::assertTrue($step->isEnabled());
            },
        ];

        yield [
            <<<YAML
            type: currency
            mode: create
            code: bar
            subunits: 4
            enabled: false
            YAML,
            static function (object $step): void {
                self::assertInstanceOf(CurrencyCreateStep::class, $step);
                self::assertSame('bar', $step->getCode());
                self::assertSame(4, $step->getSubunits());
                self::assertFalse($step->isEnabled());
            },
        ];
    }
}
