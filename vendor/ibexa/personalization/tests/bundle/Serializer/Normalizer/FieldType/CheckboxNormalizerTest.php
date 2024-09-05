<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Personalization\Serializer\Normalizer\FieldType;

use Ibexa\Bundle\Personalization\Serializer\Normalizer\FieldType\CheckboxNormalizer;
use Ibexa\Contracts\Personalization\Serializer\Normalizer\ValueNormalizerInterface;
use Ibexa\Core\FieldType\Checkbox\Value;

/**
 * @covers \Ibexa\Bundle\Personalization\Serializer\Normalizer\FieldType\CheckboxNormalizer
 */
final class CheckboxNormalizerTest extends AbstractValueNormalizerTestCase
{
    /**
     * @dataProvider provideDataForTestNormalize
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function testNormalizer(bool $expected, Value $value): void
    {
        $this->testNormalize($expected, $value);
    }

    /**
     * @return iterable<array{
     *  bool,
     *  \Ibexa\Core\FieldType\Checkbox\Value
     * }>
     */
    public function provideDataForTestNormalize(): iterable
    {
        yield [
            false,
            new Value(),
        ];

        yield [
            true,
            new Value(true),
        ];
    }

    protected function getNormalizer(): ValueNormalizerInterface
    {
        return new CheckboxNormalizer();
    }

    protected function getValue(): Value
    {
        return new Value();
    }
}
