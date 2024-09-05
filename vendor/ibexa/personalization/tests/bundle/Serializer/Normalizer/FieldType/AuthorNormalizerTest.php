<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Personalization\Serializer\Normalizer\FieldType;

use Ibexa\Bundle\Personalization\Serializer\Normalizer\FieldType\AuthorNormalizer;
use Ibexa\Contracts\Personalization\Serializer\Normalizer\ValueNormalizerInterface;
use Ibexa\Core\FieldType\Author\Author as CoreAuthor;
use Ibexa\Core\FieldType\Author\Value;

/**
 * @covers \Ibexa\Bundle\Personalization\Serializer\Normalizer\FieldType\AuthorNormalizer
 */
final class AuthorNormalizerTest extends AbstractValueNormalizerTestCase
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
     *  \Ibexa\Core\FieldType\Author\Value
     * }>
     */
    public function provideDataForTestNormalize(): iterable
    {
        yield [
            ['foo', 'bar'],
            new Value(
                [
                    new CoreAuthor(
                        [
                            'id' => 1,
                            'name' => 'foo',
                            'email' => 'foo@link.invalid',
                        ]
                    ),
                    new CoreAuthor(
                        [
                            'id' => 2,
                            'name' => 'bar',
                            'email' => 'bar@link.invalid',
                        ]
                    ),
                ]
            ),
        ];

        yield [
            [],
            new Value(),
        ];
    }

    protected function getNormalizer(): ValueNormalizerInterface
    {
        return new AuthorNormalizer();
    }

    protected function getValue(): Value
    {
        return new Value();
    }
}
