<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Migrations\CustomerGroup;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\Query\Criterion\CustomerGroupIdentifier;
use Ibexa\ProductCatalog\Migrations\CustomerGroup\CustomerGroupDeleteStep;
use Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractNormalizerTest;

/**
 * @covers \Ibexa\ProductCatalog\Migrations\CustomerGroup\CustomerGroupDeleteStepNormalizer
 *
 * @extends \Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractNormalizerTest<\Ibexa\ProductCatalog\Migrations\CustomerGroup\CustomerGroupDeleteStep>
 */
final class CustomerGroupDeleteStepNormalizerTest extends AbstractNormalizerTest
{
    protected static function getHandledClass(): string
    {
        return CustomerGroupDeleteStep::class;
    }

    public function provideForSerialization(): iterable
    {
        yield [
            new CustomerGroupDeleteStep(
                new CustomerGroupIdentifier('foo'),
            ),
            <<<YAML
            type: customer_group
            mode: delete
            criteria:
                type: field_value
                field: identifier
                value: foo
                operator: '='

            YAML,
        ];
    }

    public function provideForDeserialization(): iterable
    {
        $yaml = <<<YAML
            type: customer_group
            mode: delete
            criteria:
                type: field_value
                field: identifier
                value: foo
            YAML;

        $expected = static function (object $step): void {
            self::assertInstanceOf(CustomerGroupDeleteStep::class, $step);
            $criterion = $step->getCriterion();
            self::assertInstanceOf(FieldValueCriterion::class, $criterion);
            self::assertSame('identifier', $criterion->getField());
            self::assertSame('foo', $criterion->getValue());
            self::assertSame('=', $criterion->getOperator());
        };

        yield [
            $yaml,
            $expected,
        ];
    }
}
