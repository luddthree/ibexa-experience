<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Measurement\ProductCatalog\Form\Attribute\Search\Criterion;

use Ibexa\Contracts\Measurement\MeasurementServiceInterface;
use Ibexa\Contracts\Measurement\Product\Query\Criterion\SimpleMeasurementAttribute;
use Ibexa\Contracts\Measurement\Value\SimpleValueInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalOr;
use Ibexa\Solr\Handler as SolrHandler;

/**
 * @covers \Ibexa\Measurement\ProductCatalog\Form\Attribute\Search\Criterion\SimpleMeasurementAttributeVisitor
 */
final class SimpleMeasurementAttributeTest extends AbstractMeasurementAttributeTest
{
    private const PRODUCT_0_7CM = '0_7cm';
    private const PRODUCT_42CM = '42cm';
    private const PRODUCT_0_000_005CM = '0_000_005cm';
    private const PRODUCT_0_333YARD = '0_333yard';

    protected function setUp(): void
    {
        parent::setUp();

        $this->addProduct('EMPTY', []);
        $this->addProduct(self::PRODUCT_0_7CM, [
            self::ATTRIBUTE_SIMPLE_FOO => self::buildMeasurementAttribute(0.7, 'centimeter'),
        ]);
        $this->addProduct(self::PRODUCT_42CM, [
            self::ATTRIBUTE_SIMPLE_FOO => self::buildMeasurementAttribute(42, 'centimeter'),
        ]);
        $this->addProduct(self::PRODUCT_0_000_005CM, [
            self::ATTRIBUTE_SIMPLE_FOO => self::buildMeasurementAttribute(0.000_005, 'centimeter'),
        ]);
        $this->addProduct(self::PRODUCT_0_333YARD, [
            self::ATTRIBUTE_SIMPLE_FOO => self::buildMeasurementAttribute(0.333, 'yard'),
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
            string $unit
        ): callable {
            return static fn (): SimpleMeasurementAttribute => self::buildMeasurementCriterion($value, $unit);
        };

        yield '= 0.7cm' => [
            [self::PRODUCT_0_7CM],
            $creator(0.7, 'centimeter'),
        ];

        yield '= 30.449890270666cm (0.333 yards)' => [
            [self::PRODUCT_0_333YARD],
            $creator(0.333 / 1.0936 * 100, 'centimeter'),
        ];

        yield '= 0.7cm (0.0076552 yards)' => [
            [self::PRODUCT_0_7CM],
            $creator(0.7 * 1.0936 / 100, 'yard'),
        ];

        yield '= 0.7cm (0.007 meters)' => [
            [self::PRODUCT_0_7CM],
            $creator(0.007, 'meter'),
        ];

        yield '= 0.9cm' => [
            [],
            $creator(0.9, 'centimeter'),
        ];

        yield '= 0.7cm AND = 42cm' => [
            [],
            static function (): LogicalAnd {
                return new LogicalAnd([
                    self::buildMeasurementCriterion(0.7, 'centimeter'),
                    self::buildMeasurementCriterion(0.000_005, 'centimeter'),
                ]);
            },
        ];

        yield '= 0.7cm OR = 0.000_005cm' => [
            [self::PRODUCT_0_7CM, self::PRODUCT_0_000_005CM],
            static function (): LogicalOr {
                return new LogicalOr([
                    self::buildMeasurementCriterion(0.7, 'centimeter'),
                    self::buildMeasurementCriterion(0.000_005, 'centimeter'),
                ]);
            },
        ];

        yield '>= 0.7cm AND <= 42cm' => [
            [self::PRODUCT_0_7CM, self::PRODUCT_42CM, self::PRODUCT_0_333YARD],
            static function (): LogicalAnd {
                $minimum = self::buildMeasurementCriterion(0.7, 'centimeter');
                $minimum->setOperator('>=');
                $maximum = self::buildMeasurementCriterion(42, 'centimeter');
                $maximum->setOperator('<=');

                return new LogicalAnd([
                    $minimum,
                    $maximum,
                ]);
            },
        ];
    }

    private static function buildMeasurementCriterion(float $value, string $unitName): SimpleMeasurementAttribute
    {
        $value = self::buildMeasurementAttribute($value, $unitName);

        return new SimpleMeasurementAttribute(self::ATTRIBUTE_SIMPLE_FOO, $value);
    }

    private static function buildMeasurementAttribute(float $value, string $unitName): SimpleValueInterface
    {
        return self::getServiceByClassName(MeasurementServiceInterface::class)->buildSimpleValue(
            'length',
            $value,
            $unitName,
        );
    }
}
