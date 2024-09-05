<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Personalization\Serializer\Normalizer\FieldType;

use Ibexa\Bundle\Personalization\Serializer\Normalizer\FieldType\BinaryFileNormalizer;
use Ibexa\Contracts\Personalization\Serializer\Normalizer\ValueNormalizerInterface;
use Ibexa\Core\FieldType\BinaryFile\Value;

/**
 * @covers \Ibexa\Bundle\Personalization\Serializer\Normalizer\FieldType\BinaryFileNormalizer
 */
final class BinaryFileNormalizerTest extends AbstractValueNormalizerTestCase
{
    /**
     * @dataProvider provideDataForTestNormalize
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function testNormalizer(?string $expected, Value $value): void
    {
        $this->testNormalize($expected, $value);
    }

    /**
     * @return iterable<array{
     *  ?string,
     *  \Ibexa\Core\FieldType\BinaryFile\Value
     * }>
     */
    public function provideDataForTestNormalize(): iterable
    {
        yield [
            'public/var/test/1/2/3/4/5/file.invalid',
            new Value(
                [
                    'id' => 1,
                    'inputUri' => 'storage/file.invalid',
                    'fileName' => 'file.invalid',
                    'fileSize' => 123456,
                    'mimeType' => 'image/png',
                    'uri' => 'public/var/test/1/2/3/4/5/file.invalid',
                    'downloadCount' => 1,
                ]
            ),
        ];

        yield [
            null,
            new Value(
                [
                    'id' => null,
                    'inputUri' => null,
                    'fileName' => null,
                    'fileSize' => null,
                    'mimeType' => null,
                    'uri' => null,
                    'downloadCount' => null,
                ]
            ),
        ];

        yield [
            null,
            new Value(),
        ];
    }

    protected function getNormalizer(): ValueNormalizerInterface
    {
        return new BinaryFileNormalizer();
    }

    protected function getValue(): Value
    {
        return new Value();
    }
}
