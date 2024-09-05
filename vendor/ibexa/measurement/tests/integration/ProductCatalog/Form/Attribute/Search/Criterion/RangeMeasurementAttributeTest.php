<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Measurement\ProductCatalog\Form\Attribute\Search\Criterion;

use Ibexa\Contracts\Measurement\MeasurementServiceInterface;
use Ibexa\Contracts\Measurement\Product\Query\Criterion\AbstractMeasurementAttribute;
use Ibexa\Contracts\Measurement\Product\Query\Criterion\RangeMeasurementAttributeMaximum;
use Ibexa\Contracts\Measurement\Product\Query\Criterion\RangeMeasurementAttributeMinimum;
use Ibexa\Contracts\Measurement\Value\RangeValueInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalOr;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;
use Ibexa\Solr\Handler as SolrHandler;
use InvalidArgumentException;

/**
 * @covers \Ibexa\Measurement\ProductCatalog\Form\Attribute\Search\Criterion\RangeMeasurementAttributeMaximumVisitor
 * @covers \Ibexa\Measurement\ProductCatalog\Form\Attribute\Search\Criterion\RangeMeasurementAttributeMinimumVisitor
 */
final class RangeMeasurementAttributeTest extends AbstractMeasurementAttributeTest
{
    private const PRODUCT_0_7CM__0_9CM = '0_7cm-0_9cm';
    private const PRODUCT_7CM__42CM = '7cm-42cm';
    private const PRODUCT_0_000_001CM__0_000_005CM = '0_000_001cm-0_000_005cm';
    private const PRODUCT_0_333YARD__0_333YARD = '0_333yard-0_333yard';

    protected function setUp(): void
    {
        parent::setUp();

        $this->addProduct('EMPTY', []);
        $this->addProduct(self::PRODUCT_0_7CM__0_9CM, [
            self::ATTRIBUTE_RANGE_FOO => $this->buildMeasurementAttribute(0.7, 0.9, 'centimeter'),
        ]);
        $this->addProduct(self::PRODUCT_7CM__42CM, [
            self::ATTRIBUTE_RANGE_FOO => $this->buildMeasurementAttribute(7, 42, 'centimeter'),
        ]);
        $this->addProduct(self::PRODUCT_0_000_001CM__0_000_005CM, [
            self::ATTRIBUTE_RANGE_FOO => $this->buildMeasurementAttribute(0.000_001, 0.000_005, 'centimeter'),
        ]);
        $this->addProduct(self::PRODUCT_0_333YARD__0_333YARD, [
            self::ATTRIBUTE_RANGE_FOO => $this->buildMeasurementAttribute(0.333, 0.333, 'yard'),
        ]);

        if (getenv('SEARCH_ENGINE') === 'solr') {
            $handler = self::getServiceByClassName(SolrHandler::class, SolrHandler::class);
            $handler->commit();
        }
    }

    public function provideForQuerying(): iterable
    {
        $creator = static function (
            float $value,
            string $unit,
            string $type
        ): callable {
            return static fn (): AbstractMeasurementAttribute => self::buildMeasurementCriterion($value, $unit, $type);
        };

        yield 'minimum = 0.7cm' => [
            [self::PRODUCT_0_7CM__0_9CM],
            $creator(0.7, 'centimeter', 'min'),
        ];

        yield 'maximum = 0.9cm' => [
            [self::PRODUCT_0_7CM__0_9CM],
            $creator(0.9, 'centimeter', 'max'),
        ];

        yield 'minimum = 0.007 meters' => [
            [self::PRODUCT_0_7CM__0_9CM],
            $creator(0.007, 'meter', 'min'),
        ];

        yield 'maximum = 0.009 meters' => [
            [self::PRODUCT_0_7CM__0_9CM],
            $creator(0.009, 'meter', 'max'),
        ];

        yield 'minimum = 0.000_001cm' => [
            [self::PRODUCT_0_000_001CM__0_000_005CM],
            $creator(0.000_001, 'centimeter', 'min'),
        ];

        yield 'maximum = 0.000_005cm' => [
            [self::PRODUCT_0_000_001CM__0_000_005CM],
            $creator(0.000_005, 'centimeter', 'max'),
        ];

        yield 'minimum = 30.449890270666cm (0.333 yards)' => [
            [self::PRODUCT_0_333YARD__0_333YARD],
            $creator(0.333 / 1.0936 * 100, 'centimeter', 'min'),
        ];

        yield 'maximum = 30.449890270666cm (0.333 yards)' => [
            [self::PRODUCT_0_333YARD__0_333YARD],
            $creator(0.333 / 1.0936 * 100, 'centimeter', 'max'),
        ];

        yield 'minimum = 0.7cm AND maximum = 0.9cm' => [
            [self::PRODUCT_0_7CM__0_9CM],
            static function (): CriterionInterface {
                return new LogicalAnd([
                    self::buildMeasurementCriterion(0.7, 'centimeter', 'min'),
                    self::buildMeasurementCriterion(0.9, 'centimeter', 'max'),
                ]);
            },
        ];

        yield 'minimum = 0.7cm OR maximum = 0.000_005cm' => [
            [self::PRODUCT_0_7CM__0_9CM, self::PRODUCT_0_000_001CM__0_000_005CM],
            static function (): CriterionInterface {
                return new LogicalOr([
                    self::buildMeasurementCriterion(0.7, 'centimeter', 'min'),
                    self::buildMeasurementCriterion(0.000_005, 'centimeter', 'max'),
                ]);
            },
        ];

        yield 'minimum >= 0.7cm AND maximum <= 42cm' => [
            [self::PRODUCT_0_7CM__0_9CM, self::PRODUCT_7CM__42CM, self::PRODUCT_0_333YARD__0_333YARD],
            static function (): CriterionInterface {
                $minimum = self::buildMeasurementCriterion(0.7, 'centimeter', 'min');
                $minimum->setOperator('>=');

                $maximum = self::buildMeasurementCriterion(42, 'centimeter', 'max');
                $maximum->setOperator('<=');

                return new LogicalAnd([
                    $minimum,
                    $maximum,
                ]);
            },
        ];
    }

    private static function buildMeasurementAttribute(float $minValue, float $maxValue, string $unitName): RangeValueInterface
    {
        return self::getServiceByClassName(MeasurementServiceInterface::class)->buildRangeValue(
            'length',
            $minValue,
            $maxValue,
            $unitName,
        );
    }

    private static function buildMeasurementCriterion(float $value, string $unitName, string $type): AbstractMeasurementAttribute
    {
        $value = self::getServiceByClassName(MeasurementServiceInterface::class)->buildSimpleValue(
            'length',
            $value,
            $unitName,
        );

        if ($type === 'max') {
            return new RangeMeasurementAttributeMaximum(self::ATTRIBUTE_RANGE_FOO, $value);
        } elseif ($type === 'min') {
            return new RangeMeasurementAttributeMinimum(self::ATTRIBUTE_RANGE_FOO, $value);
        } else {
            throw new InvalidArgumentException(sprintf(
                'Expected one of: "%s". Received "%s".',
                implode('","', ['min', 'max']),
                $type,
            ));
        }
    }
}
