<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Migrations\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\ProductCatalog\Migrations\Criterion\FieldValueCriterionNormalizer;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion
 */
final class FieldValueCriterionNormalizerTest extends TestCase
{
    private FieldValueCriterionNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new FieldValueCriterionNormalizer();
    }

    public function testDenormalize(): void
    {
        $data = [
            'type' => 'field_value',
            'field' => 'foo_field',
            'value' => 'foo_value',
        ];
        $result = $this->normalizer->denormalize($data, CriterionInterface::class);
        self::assertInstanceOf(FieldValueCriterion::class, $result);
        self::assertSame('foo_field', $result->getField());
        self::assertSame('foo_value', $result->getValue());
        self::assertSame('=', $result->getOperator());

        $data = [
            'type' => 'field_value',
            'field' => 'foo_field',
            'value' => 'foo_value',
            'operator' => 'IN',
        ];
        $result = $this->normalizer->denormalize($data, CriterionInterface::class);
        self::assertInstanceOf(FieldValueCriterion::class, $result);
        self::assertSame('foo_field', $result->getField());
        self::assertSame('foo_value', $result->getValue());
        self::assertSame('IN', $result->getOperator());
    }

    public function testNormalize(): void
    {
        $criterion = new FieldValueCriterion('field_foo', 'value_foo');
        $result = $this->normalizer->normalize($criterion);

        self::assertSame([
            'type' => 'field_value',
            'field' => 'field_foo',
            'value' => 'value_foo',
            'operator' => '=',
        ], $result);
    }

    public function testSupportsNormalization(): void
    {
        self::assertFalse($this->normalizer->supportsNormalization((object)[]));
        self::assertFalse($this->normalizer->supportsNormalization(
            $this->createMock(CriterionInterface::class),
        ));
        self::assertTrue($this->normalizer->supportsNormalization(
            $this->createMock(FieldValueCriterion::class),
        ));
    }

    public function testSupportsDenormalization(): void
    {
        self::assertFalse($this->normalizer->supportsDenormalization(null, 'foo'));
        self::assertFalse($this->normalizer->supportsDenormalization(null, CriterionInterface::class));
        self::assertTrue($this->normalizer->supportsDenormalization(
            ['type' => 'field_value'],
            CriterionInterface::class,
        ));
    }
}
