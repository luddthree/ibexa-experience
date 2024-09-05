<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Migrations\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\LogicalOr;
use Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractNormalizerTest;

/**
 * @covers \Ibexa\ProductCatalog\Migrations\Criterion\CriterionNormalizer
 *
 * @extends \Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractNormalizerTest<\Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface>
 */
final class CriterionNormalizerTest extends AbstractNormalizerTest
{
    private const FOO_FIELD = 'foo_field';
    private const FOO_VALUE = 'foo_value';
    private const BAR_FIELD = 'bar_field';
    private const BAR_VALUE = 'bar_value';

    protected static function getHandledClass(): string
    {
        return CriterionInterface::class;
    }

    public function provideForSerialization(): iterable
    {
        $fooFieldValueCriterionString = self::getFieldValueCriterionYaml(self::FOO_FIELD, self::FOO_VALUE);

        yield [
            new FieldValueCriterion(self::FOO_FIELD, self::FOO_VALUE),
            <<<YAML
            $fooFieldValueCriterionString
            operator: '='

            YAML,
        ];

        $barFieldValueCriterionString = self::getFieldValueCriterionYaml(self::BAR_FIELD, self::BAR_VALUE);

        yield [
            new LogicalAnd(
                new FieldValueCriterion(self::FOO_FIELD, self::FOO_VALUE),
                new FieldValueCriterion(self::BAR_FIELD, self::BAR_VALUE, 'STARTS_WITH'),
            ),
            <<<YAML
            type: and
            criteria:
                -
                    {$this->indent($fooFieldValueCriterionString, 8)}
                    operator: '='
                -
                    {$this->indent($barFieldValueCriterionString, 8)}
                    operator: STARTS_WITH

            YAML,
        ];

        yield [
            new LogicalOr(
                new FieldValueCriterion(self::FOO_FIELD, self::FOO_VALUE),
                new FieldValueCriterion(self::BAR_FIELD, self::BAR_VALUE, 'ENDS_WITH'),
            ),
            <<<YAML
            type: or
            criteria:
                -
                    {$this->indent($fooFieldValueCriterionString, 8)}
                    operator: '='
                -
                    {$this->indent($barFieldValueCriterionString, 8)}
                    operator: ENDS_WITH

            YAML,
        ];

        yield [
            new LogicalAnd(
                new FieldValueCriterion(self::FOO_FIELD, self::FOO_VALUE),
                new LogicalOr(
                    new FieldValueCriterion(self::BAR_FIELD, self::BAR_VALUE, 'STARTS_WITH'),
                    new FieldValueCriterion(self::BAR_FIELD, self::BAR_VALUE, 'ENDS_WITH'),
                ),
            ),
            <<<YAML
            type: and
            criteria:
                -
                    {$this->indent($fooFieldValueCriterionString, 8)}
                    operator: '='
                -
                    type: or
                    criteria:
                        -
                            {$this->indent($barFieldValueCriterionString, 16)}
                            operator: STARTS_WITH
                        -
                            {$this->indent($barFieldValueCriterionString, 16)}
                            operator: ENDS_WITH

            YAML,
        ];
    }

    public function provideForDeserialization(): iterable
    {
        $fooFieldValueCriterionString = self::getFieldValueCriterionYaml(self::FOO_FIELD, self::FOO_VALUE);

        yield [
            $fooFieldValueCriterionString,
            static function (object $criterion): void {
                self::assertInstanceOf(FieldValueCriterion::class, $criterion);
                self::assertFieldValueCriterionState(
                    $criterion,
                    self::FOO_FIELD,
                    self::FOO_VALUE,
                );
            },
        ];

        $barFieldValueCriterionString = self::getFieldValueCriterionYaml(self::BAR_FIELD, self::BAR_VALUE);

        yield [
            <<<YAML
            type: and
            criteria:
                -   {$this->indent($fooFieldValueCriterionString, 8)}
                -
                    {$this->indent($barFieldValueCriterionString, 8)}
                    operator: STARTS_WITH
            YAML,
            static function (object $criterion): void {
                self::assertInstanceOf(LogicalAnd::class, $criterion);
                $criteria = $criterion->getCriteria();

                self::assertArrayHasKey(0, $criteria);
                $subCriterion = $criteria[0];
                self::assertInstanceOf(FieldValueCriterion::class, $subCriterion);
                self::assertFieldValueCriterionState(
                    $subCriterion,
                    self::FOO_FIELD,
                    self::FOO_VALUE,
                );

                self::assertArrayHasKey(1, $criteria);
                $subCriterion = $criteria[1];
                self::assertInstanceOf(FieldValueCriterion::class, $subCriterion);
                self::assertFieldValueCriterionState(
                    $subCriterion,
                    self::BAR_FIELD,
                    self::BAR_VALUE,
                    'STARTS_WITH',
                );
            },
        ];

        yield [
            <<<YAML
            type: or
            criteria:
                -   {$this->indent($fooFieldValueCriterionString, 8)}
                -
                    {$this->indent($barFieldValueCriterionString, 8)}
                    operator: ENDS_WITH
            YAML,
            static function (object $criterion): void {
                self::assertInstanceOf(LogicalOr::class, $criterion);
                $criteria = $criterion->getCriteria();

                self::assertArrayHasKey(0, $criteria);
                $subCriterion = $criteria[0];
                self::assertInstanceOf(FieldValueCriterion::class, $subCriterion);
                self::assertFieldValueCriterionState(
                    $subCriterion,
                    self::FOO_FIELD,
                    self::FOO_VALUE,
                );

                self::assertArrayHasKey(1, $criteria);
                $subCriterion = $criteria[1];
                self::assertInstanceOf(FieldValueCriterion::class, $subCriterion);
                self::assertFieldValueCriterionState(
                    $subCriterion,
                    self::BAR_FIELD,
                    self::BAR_VALUE,
                    'ENDS_WITH',
                );
            },
        ];

        yield [
            <<<YAML
            type: and
            criteria:
                -   {$this->indent($fooFieldValueCriterionString, 8)}
                -   type: or
                    criteria:
                        -   {$this->indent($barFieldValueCriterionString, 16)}
                            operator: STARTS_WITH
                        -   {$this->indent($barFieldValueCriterionString, 16)}
                            operator: ENDS_WITH

            YAML,
            static function (object $criterion): void {
                self::assertInstanceOf(LogicalAnd::class, $criterion);
                $criteria = $criterion->getCriteria();

                self::assertArrayHasKey(0, $criteria);
                $subCriterion = $criteria[0];
                self::assertInstanceOf(FieldValueCriterion::class, $subCriterion);
                self::assertFieldValueCriterionState(
                    $subCriterion,
                    self::FOO_FIELD,
                    self::FOO_VALUE,
                );

                self::assertArrayHasKey(1, $criteria);
                $criterion = $criteria[1];
                self::assertInstanceOf(LogicalOr::class, $criterion);
                $criteria = $criterion->getCriteria();

                self::assertArrayHasKey(0, $criteria);
                $subCriterion = $criteria[0];
                self::assertInstanceOf(FieldValueCriterion::class, $subCriterion);
                self::assertFieldValueCriterionState(
                    $subCriterion,
                    self::BAR_FIELD,
                    self::BAR_VALUE,
                    'STARTS_WITH',
                );

                self::assertArrayHasKey(1, $criteria);
                $subCriterion = $criteria[1];
                self::assertInstanceOf(FieldValueCriterion::class, $subCriterion);
                self::assertFieldValueCriterionState(
                    $subCriterion,
                    self::BAR_FIELD,
                    self::BAR_VALUE,
                    'ENDS_WITH',
                );
            },
        ];
    }

    private static function getFieldValueCriterionYaml(string $field, string $value): string
    {
        return <<<YAML
        type: field_value
        field: $field
        value: $value
        YAML;
    }

    private static function assertFieldValueCriterionState(
        FieldValueCriterion $criterion,
        string $field,
        string $value,
        string $operator = '='
    ): void {
        self::assertSame($field, $criterion->getField());
        self::assertSame($value, $criterion->getValue());
        self::assertSame($operator, $criterion->getOperator());
    }

    private function indent(string $string, int $spaces): string
    {
        $exploded = explode("\n", $string);
        $firstElement = $exploded[0];
        array_shift($exploded);
        $exploded = array_map(
            static fn (string $string): string => str_repeat(' ', $spaces) . $string,
            $exploded,
        );

        $exploded = [
            $firstElement,
            ...$exploded,
        ];

        return implode("\n", $exploded);
    }
}
