<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Personalization\Serializer\Normalizer\FieldType;

use Ibexa\Bundle\Personalization\Serializer\Normalizer\FieldType\KeywordNormalizer;
use Ibexa\Contracts\Personalization\Serializer\Normalizer\ValueNormalizerInterface;
use Ibexa\Core\FieldType\Keyword\Value;

/**
 * @covers \Ibexa\Bundle\Personalization\Serializer\Normalizer\FieldType\KeywordNormalizer
 */
final class KeywordNormalizerTest extends AbstractValueNormalizerTestCase
{
    /**
     * @dataProvider provideDataForTestNormalize
     *
     * @param array<string> $expected
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function testNormalizer(array $expected, Value $value): void
    {
        $this->testNormalize($expected, $value);
    }

    /**
     * @return iterable<array{
     *  array<string>,
     *  \Ibexa\Core\FieldType\Keyword\Value
     * }>
     */
    public function provideDataForTestNormalize(): iterable
    {
        yield [
            ['foo'],
            new Value('foo'),
        ];

        yield [
            ['foo', 'bar', 'baz'],
            new Value(['foo', 'bar', 'baz']),
        ];

        yield [
            [],
            new Value(),
        ];
    }

    protected function getNormalizer(): ValueNormalizerInterface
    {
        return new KeywordNormalizer();
    }

    protected function getValue(): Value
    {
        return new Value();
    }
}
