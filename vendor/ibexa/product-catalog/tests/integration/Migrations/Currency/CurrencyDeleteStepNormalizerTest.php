<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Migrations\Currency;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Currency\Query\Criterion\CurrencyCodeCriterion;
use Ibexa\ProductCatalog\Migrations\Currency\CurrencyDeleteStep;
use Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractNormalizerTest;

/**
 * @covers \Ibexa\ProductCatalog\Migrations\Currency\CurrencyDeleteStepNormalizer
 *
 * @extends \Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractNormalizerTest<\Ibexa\ProductCatalog\Migrations\Currency\CurrencyDeleteStep>
 */
final class CurrencyDeleteStepNormalizerTest extends AbstractNormalizerTest
{
    protected static function getHandledClass(): string
    {
        return CurrencyDeleteStep::class;
    }

    public function provideForSerialization(): iterable
    {
        yield [
            new CurrencyDeleteStep(
                new CurrencyCodeCriterion('EUR'),
            ),
            <<<YAML
            type: currency
            mode: delete
            criteria:
                type: field_value
                field: code
                value: EUR
                operator: '='

            YAML,
        ];

        yield [
            new CurrencyDeleteStep(
                new FieldValueCriterion('enabled', false),
            ),
            <<<YAML
            type: currency
            mode: delete
            criteria:
                type: field_value
                field: enabled
                value: false
                operator: '='

            YAML,
        ];
    }

    public function provideForDeserialization(): iterable
    {
        yield [
            <<<YAML
            type: currency
            mode: delete
            criteria:
                type: field_value
                field: foo_field
                value: foo_value
            YAML,
            static function (object $step): void {
                self::assertInstanceOf(CurrencyDeleteStep::class, $step);

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
            mode: delete
            criteria:
                type: field_value
                field: code
                value: foo_value
            YAML,
            static function (object $step): void {
                self::assertInstanceOf(CurrencyDeleteStep::class, $step);

                $criterion = $step->getCriterion();
                self::assertInstanceOf(CurrencyCodeCriterion::class, $criterion);
                self::assertSame('code', $criterion->getField());
                self::assertSame('foo_value', $criterion->getValue());
                self::assertSame('=', $criterion->getOperator());
            },
        ];
    }
}
