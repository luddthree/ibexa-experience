<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Migrations\Currency;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Currency\Query\Criterion\CurrencyCodeCriterion;
use Ibexa\ProductCatalog\Migrations\Currency\CurrencyUpdateStep;
use Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractNormalizerTest;

/**
 * @covers \Ibexa\ProductCatalog\Migrations\Currency\CurrencyUpdateStepNormalizer
 *
 * @extends \Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractNormalizerTest<\Ibexa\ProductCatalog\Migrations\Currency\CurrencyUpdateStep>
 */
final class CurrencyUpdateStepNormalizerTest extends AbstractNormalizerTest
{
    protected static function getHandledClass(): string
    {
        return CurrencyUpdateStep::class;
    }

    public function provideForSerialization(): iterable
    {
        yield [
            new CurrencyUpdateStep(
                new CurrencyCodeCriterion('EUR'),
                null,
                null,
                null,
            ),
            <<<YAML
            type: currency
            mode: update
            criteria:
                type: field_value
                field: code
                value: EUR
                operator: '='
            code: null
            subunits: null
            enabled: null

            YAML,
        ];

        yield [
            new CurrencyUpdateStep(
                new CurrencyCodeCriterion('EUR'),
                'FOO',
                3,
                false,
            ),
            <<<YAML
            type: currency
            mode: update
            criteria:
                type: field_value
                field: code
                value: EUR
                operator: '='
            code: FOO
            subunits: 3
            enabled: false

            YAML,
        ];
    }

    public function provideForDeserialization(): iterable
    {
        yield [
            <<<YAML
            type: currency
            mode: update
            criteria:
                type: field_value
                field: foo_field
                value: foo_value
            YAML,
            static function (object $step): void {
                self::assertInstanceOf(CurrencyUpdateStep::class, $step);
                self::assertNull($step->getCode());
                self::assertNull($step->getSubunits());
                self::assertNull($step->isEnabled());

                $criterion = $step->getCriterion();
                self::assertInstanceOf(FieldValueCriterion::class, $criterion);
                self::assertSame('foo_field', $criterion->getField());
                self::assertSame('foo_value', $criterion->getValue());
                self::assertSame('=', $criterion->getOperator());
            },
        ];

        yield [
            <<<YAML
            type: currency
            mode: update
            criteria:
                type: field_value
                field: code
                value: EUR
                operator: '='
            code: FOO
            subunits: 3
            enabled: false

            YAML,
            static function (object $step): void {
                self::assertInstanceOf(CurrencyUpdateStep::class, $step);
                self::assertSame('FOO', $step->getCode());
                self::assertSame(3, $step->getSubunits());
                self::assertFalse($step->isEnabled());

                $criterion = $step->getCriterion();
                self::assertInstanceOf(CurrencyCodeCriterion::class, $criterion);
                self::assertSame('code', $criterion->getField());
                self::assertSame('EUR', $criterion->getValue());
                self::assertSame('=', $criterion->getOperator());
            },
        ];
    }
}
