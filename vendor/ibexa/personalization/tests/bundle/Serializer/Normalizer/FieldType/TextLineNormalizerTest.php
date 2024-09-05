<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Personalization\Serializer\Normalizer\FieldType;

use Ibexa\Bundle\Personalization\Serializer\Normalizer\FieldType\TextLineNormalizer;
use Ibexa\Contracts\Personalization\Serializer\Normalizer\ValueNormalizerInterface;
use Ibexa\Core\FieldType\TextBlock\Value;

/**
 * @covers \Ibexa\Bundle\Personalization\Serializer\Normalizer\FieldType\TextLineNormalizer
 */
final class TextLineNormalizerTest extends AbstractValueNormalizerTestCase
{
    /**
     * @dataProvider provideDataForTestNormalize
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function testNormalizer(string $expected, Value $value): void
    {
        $this->testNormalize($expected, $value);
    }

    /**
     * @return iterable<array{
     *  string,
     *  \Ibexa\Core\FieldType\TextBlock\Value
     * }>
     */
    public function provideDataForTestNormalize(): iterable
    {
        yield [
            'Lorem Ipsum',
            new Value('Lorem Ipsum'),
        ];

        yield [
            '',
            new Value(),
        ];
    }

    protected function getNormalizer(): ValueNormalizerInterface
    {
        return new TextLineNormalizer();
    }

    protected function getValue(): Value
    {
        return new Value();
    }
}
